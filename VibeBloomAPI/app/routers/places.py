from pathlib import Path
from uuid import uuid4

from fastapi import APIRouter, Depends, File, HTTPException, status, UploadFile
from sqlalchemy import select
from sqlalchemy.orm import Session, selectinload

from app.core.database import get_db
from app.core.security import get_current_user
from app.models.place import Place
from app.models.user import User
from app.models.review import Review
from app.models.review_reply import ReviewReply
from app.schemas.place import PlaceCreate, PlaceResponse, PlaceUpdate

router = APIRouter(prefix="/places", tags=["Places"])
BASE_DIR = Path(__file__).resolve().parents[2]
PLACE_STORAGE_DIR = BASE_DIR / "storage" / "places"
ALLOWED_IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".webp"}


def is_admin(user: User) -> bool:
    return str(getattr(user, "role", "") or "").lower() == "admin"


def can_manage_place(current_user: User, place: Place) -> bool:
    if is_admin(current_user):
        return True

    return place.user_id == current_user.id


def get_place_base_query():
    return (
        select(Place)
        .options(
            selectinload(Place.user),
            selectinload(Place.reviews).selectinload(Review.user),
            selectinload(Place.reviews).selectinload(Review.replies).selectinload(ReviewReply.user),
        )
    )


def get_place_with_relations(db: Session, place_id: int):
    result = db.execute(
        get_place_base_query().where(Place.id == place_id)
    )
    return result.scalars().unique().first()


def get_place_or_404(db: Session, place_id: int) -> Place:
    place = db.get(Place, place_id)
    if not place:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Lugar no encontrado",
        )
    return place


def save_place_photo(file: UploadFile) -> str:
    extension = Path(file.filename or "").suffix.lower()
    if extension not in ALLOWED_IMAGE_EXTENSIONS:
        raise HTTPException(status_code=422, detail="Formato de imagen no permitido")
    if file.content_type not in {"image/jpeg", "image/png", "image/webp"}:
        raise HTTPException(status_code=422, detail="El archivo debe ser una imagen JPG, PNG o WEBP")

    contents = file.file.read()
    if not contents:
        raise HTTPException(status_code=422, detail="La imagen está vacía")
    if len(contents) > 5 * 1024 * 1024:
        raise HTTPException(status_code=422, detail="Cada imagen debe pesar máximo 5 MB")

    PLACE_STORAGE_DIR.mkdir(parents=True, exist_ok=True)
    filename = f"{uuid4().hex}{extension}"
    (PLACE_STORAGE_DIR / filename).write_bytes(contents)
    return f"places/{filename}"


@router.get("", response_model=list[PlaceResponse])
def list_places(db: Session = Depends(get_db)):
    result = db.execute(
        get_place_base_query().order_by(Place.id.desc())
    )
    return result.scalars().unique().all()


@router.get("/mine", response_model=list[PlaceResponse])
def my_places(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    result = db.execute(
        get_place_base_query()
        .where(Place.user_id == current_user.id)
        .order_by(Place.id.desc())
    )
    return result.scalars().unique().all()


@router.get("/{place_id}", response_model=PlaceResponse)
def get_place(
    place_id: int,
    db: Session = Depends(get_db)
):
    place = get_place_with_relations(db, place_id)

    if not place:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Lugar no encontrado",
        )

    return place


@router.post("", response_model=PlaceResponse, status_code=201)
def create_place(
    payload: PlaceCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    data = payload.model_dump(exclude={"photo_url", "photos_urls", "price_range"})
    data["user_id"] = current_user.id

    place = Place(**data)
    db.add(place)
    db.commit()
    db.refresh(place)

    created_place = get_place_with_relations(db, place.id)

    if not created_place:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="No se pudo recuperar el lugar creado",
        )

    return created_place


@router.put("/{place_id}", response_model=PlaceResponse)
def update_place(
    place_id: int,
    payload: PlaceUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    place = get_place_or_404(db, place_id)

    if not can_manage_place(current_user, place):
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No autorizado",
        )

    data = payload.model_dump(exclude_unset=True, exclude={"price_range"})
    data.pop("user_id", None)

    for key, value in data.items():
        setattr(place, key, value)

    db.commit()
    db.refresh(place)

    updated_place = get_place_with_relations(db, place.id)

    if not updated_place:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="No se pudo recuperar el lugar actualizado",
        )

    return updated_place


@router.post("/{place_id}/photos", response_model=PlaceResponse)
def replace_place_photos(
    place_id: int,
    photos: list[UploadFile] = File(...),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    place = get_place_or_404(db, place_id)
    if not can_manage_place(current_user, place):
        raise HTTPException(status_code=status.HTTP_403_FORBIDDEN, detail="No autorizado")
    if not 1 <= len(photos) <= 3:
        raise HTTPException(status_code=422, detail="Selecciona entre 1 y 3 fotos")

    saved_photos = [save_place_photo(photo) for photo in photos]
    place.photo = saved_photos[0]
    place.photos = saved_photos
    db.commit()

    return get_place_with_relations(db, place.id)


@router.delete("/{place_id}", status_code=status.HTTP_200_OK)
def delete_place(
    place_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    place = get_place_or_404(db, place_id)

    if not can_manage_place(current_user, place):
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No autorizado",
        )

    db.delete(place)
    db.commit()

    return {"message": "Lugar eliminado correctamente"}
