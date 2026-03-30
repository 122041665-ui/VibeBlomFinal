from fastapi import APIRouter, Depends, HTTPException, status
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
    data = payload.model_dump()
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

    data = payload.model_dump(exclude_unset=True)
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