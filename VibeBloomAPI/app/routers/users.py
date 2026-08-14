import os
from pathlib import Path
from typing import Optional
from uuid import uuid4

from fastapi import APIRouter, Depends, File, HTTPException, status, UploadFile
from pydantic import BaseModel, EmailStr, Field
from sqlalchemy import select, text
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.config import settings
from app.core.security import hash_password, get_current_user, get_optional_current_user, require_admin, verify_password
from app.models.user import User
from app.schemas.user import UserCreate, UserResponse

router = APIRouter(prefix="/users", tags=["Users"])

ROOT_ADMIN_ID = int(os.getenv("ROOT_ADMIN_ID", "3"))
BASE_DIR = Path(__file__).resolve().parents[2]
PROFILE_STORAGE_DIR = BASE_DIR / "storage" / "profile-photos"


class RoleUpdate(BaseModel):
    role: str


class UserUpdate(BaseModel):
    name: Optional[str] = Field(default=None, min_length=1, max_length=255)
    email: Optional[EmailStr] = None
    role: Optional[str] = None
    password: Optional[str] = Field(default=None, min_length=8, max_length=255)


class MyProfileUpdate(BaseModel):
    name: str = Field(min_length=1, max_length=255)
    email: EmailStr
    profile_is_public: bool = True


class MyPasswordUpdate(BaseModel):
    current_password: str = Field(min_length=1, max_length=255)
    password: str = Field(min_length=8, max_length=255)


VALID_ROLES = ["user", "moderator", "admin"]


def validate_role(role: str):
    if role not in VALID_ROLES:
        raise HTTPException(status_code=400, detail="Rol inválido")


def get_user_or_404(db: Session, user_id: int) -> User:
    user = db.get(User, user_id)
    if not user:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return user


@router.get("/me/profile", response_model=UserResponse)
def my_profile(current_user: User = Depends(get_current_user)):
    return current_user


@router.put("/me/profile", response_model=UserResponse)
def update_my_profile(
    payload: MyProfileUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    name = payload.name.strip()
    email = str(payload.email).strip().lower()
    if not name:
        raise HTTPException(status_code=422, detail="El nombre es obligatorio")
    existing = db.execute(
        select(User).where(User.email == email, User.id != current_user.id)
    ).scalar_one_or_none()
    if existing:
        raise HTTPException(status_code=422, detail="El correo ya está registrado")
    current_user.name = name
    current_user.email = email
    current_user.profile_is_public = payload.profile_is_public
    db.commit()
    db.refresh(current_user)
    return current_user


@router.put("/me/password")
def update_my_password(
    payload: MyPasswordUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    import re

    if not verify_password(payload.current_password, current_user.password):
        raise HTTPException(status_code=422, detail="La contraseña actual no es correcta")
    if not re.search(r"[A-Z]", payload.password) or not re.search(r"[a-z]", payload.password) or not re.search(r"\d", payload.password):
        raise HTTPException(
            status_code=422,
            detail="La nueva contraseña debe incluir mayúscula, minúscula y número",
        )
    current_user.password = hash_password(payload.password)
    db.commit()
    return {"message": "Contraseña actualizada correctamente"}


@router.get("/community")
def community_users(
    db: Session = Depends(get_db),
    current_user: User | None = Depends(get_optional_current_user),
):
    query = select(User).where(User.profile_is_public.is_(True))
    if current_user:
        query = query.where(User.id != current_user.id)
    users = db.execute(query.order_by(User.name, User.id)).scalars().all()
    following_ids = set(db.execute(
        text('SELECT followed_id FROM user_follows WHERE follower_id = :uid'),
        {'uid': current_user.id},
    ).scalars().all()) if current_user else set()
    result = []
    for user in users:
        places_count = db.execute(text('SELECT COUNT(*) FROM places WHERE user_id = :uid'), {'uid': user.id}).scalar() or 0
        result.append({'id': user.id, 'name': user.name, 'email': user.email, 'role': user.role, 'profile_photo_url': user.profile_photo_url, 'places_count': places_count, 'is_following': user.id in following_ids})
    return result


@router.get("/me/network")
def my_network(db: Session = Depends(get_db), current_user: User = Depends(get_current_user)):
    followers = db.execute(text("""
        SELECT u.id, u.name, u.email, u.profile_photo_path
        FROM user_follows f JOIN users u ON u.id = f.follower_id
        WHERE f.followed_id = :uid ORDER BY f.created_at DESC
    """), {"uid": current_user.id}).mappings().all()
    following = db.execute(text("""
        SELECT u.id, u.name, u.email, u.profile_photo_path
        FROM user_follows f JOIN users u ON u.id = f.followed_id
        WHERE f.follower_id = :uid ORDER BY f.created_at DESC
    """), {"uid": current_user.id}).mappings().all()
    return {"followers_count": len(followers), "following_count": len(following),
            "followers": [dict(row) for row in followers], "following": [dict(row) for row in following]}


@router.get("/{user_id}/public-profile")
def public_profile(user_id: int, db: Session = Depends(get_db), current_user: User | None = Depends(get_optional_current_user)):
    user = get_user_or_404(db, user_id)
    if not user.profile_is_public and (not current_user or current_user.id != user_id):
        raise HTTPException(status_code=403, detail="Este perfil es privado")
    followers_count = db.execute(text("SELECT COUNT(*) FROM user_follows WHERE followed_id=:uid"), {"uid": user_id}).scalar() or 0
    following_count = db.execute(text("SELECT COUNT(*) FROM user_follows WHERE follower_id=:uid"), {"uid": user_id}).scalar() or 0
    is_following = bool(current_user and db.execute(text(
        "SELECT COUNT(*) FROM user_follows WHERE follower_id=:me AND followed_id=:uid"
    ), {"me": current_user.id, "uid": user_id}).scalar())
    places = db.execute(text("""
        SELECT id, name, city, type, rating, price, photo, photos, description, address, lat, lng
        FROM places WHERE user_id=:uid ORDER BY id DESC
    """), {"uid": user_id}).mappings().all()
    place_items = []
    for row in places:
        item = dict(row)
        photo = item.get("photo")
        item["photo_url"] = f"{settings.API_PUBLIC_URL.rstrip('/')}/storage/{str(photo).lstrip('/')}" if photo else None
        place_items.append(item)
    return {"id": user.id, "name": user.name, "email": user.email,
            "profile_photo_url": user.profile_photo_url, "followers_count": followers_count,
            "following_count": following_count, "places_count": len(places),
            "is_following": is_following, "places": place_items}


@router.post("/{user_id}/follow")
def follow_user(user_id: int, db: Session = Depends(get_db), current_user: User = Depends(get_current_user)):
    if user_id == current_user.id:
        raise HTTPException(status_code=422, detail="No puedes seguirte a ti mismo")
    get_user_or_404(db, user_id)
    db.execute(text('INSERT IGNORE INTO user_follows (follower_id, followed_id, created_at, updated_at) VALUES (:me, :other, NOW(), NOW())'), {'me': current_user.id, 'other': user_id})
    db.commit()
    return {'message': 'Ahora sigues a este usuario', 'is_following': True}


@router.delete("/{user_id}/follow")
def unfollow_user(user_id: int, db: Session = Depends(get_db), current_user: User = Depends(get_current_user)):
    db.execute(text('DELETE FROM user_follows WHERE follower_id = :me AND followed_id = :other'), {'me': current_user.id, 'other': user_id})
    db.commit()
    return {'message': 'Dejaste de seguir a este usuario', 'is_following': False}


@router.post("/me/profile-photo", response_model=UserResponse)
def update_my_profile_photo(
    photo: UploadFile = File(...),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    extension = Path(photo.filename or "").suffix.lower()
    if extension not in [".jpg", ".jpeg", ".png", ".webp"]:
        raise HTTPException(status_code=422, detail="Formato de imagen no permitido")
    if photo.content_type not in {"image/jpeg", "image/png", "image/webp"}:
        raise HTTPException(status_code=422, detail="El archivo debe ser una imagen JPG, PNG o WEBP")

    contents = photo.file.read()
    if not contents:
        raise HTTPException(status_code=422, detail="La foto está vacía")
    if len(contents) > 5 * 1024 * 1024:
        raise HTTPException(status_code=422, detail="La foto debe pesar máximo 5 MB")

    PROFILE_STORAGE_DIR.mkdir(parents=True, exist_ok=True)
    filename = f"{uuid4().hex}{extension}"
    destination = PROFILE_STORAGE_DIR / filename

    with destination.open("wb") as buffer:
        buffer.write(contents)

    old_path = current_user.profile_photo_path

    current_user.profile_photo_path = f"profile-photos/{filename}"
    db.commit()
    db.refresh(current_user)

    if old_path and str(old_path).startswith("profile-photos/"):
        old_file = BASE_DIR / "storage" / str(old_path)
        if old_file.is_file() and old_file != destination:
            old_file.unlink()

    return current_user


@router.delete("/me/profile-photo", status_code=204)
def delete_my_profile_photo(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    old_path = current_user.profile_photo_path
    current_user.profile_photo_path = None
    db.commit()

    if old_path and str(old_path).startswith("profile-photos/"):
        old_file = BASE_DIR / "storage" / str(old_path)
        if old_file.is_file():
            old_file.unlink()


@router.post("/{user_id}/profile-photo", response_model=UserResponse)
def update_user_profile_photo(
    user_id: int,
    photo: UploadFile = File(...),
    db: Session = Depends(get_db),
    current_user: User = Depends(require_admin),
):
    user = get_user_or_404(db, user_id)

    extension = Path(photo.filename or "").suffix.lower()
    if extension not in [".jpg", ".jpeg", ".png", ".webp"]:
        raise HTTPException(status_code=422, detail="Formato de imagen no permitido")
    if photo.content_type not in {"image/jpeg", "image/png", "image/webp"}:
        raise HTTPException(status_code=422, detail="El archivo debe ser una imagen JPG, PNG o WEBP")

    contents = photo.file.read()
    if not contents:
        raise HTTPException(status_code=422, detail="La foto está vacía")
    if len(contents) > 5 * 1024 * 1024:
        raise HTTPException(status_code=422, detail="La foto debe pesar máximo 5 MB")

    PROFILE_STORAGE_DIR.mkdir(parents=True, exist_ok=True)
    filename = f"{uuid4().hex}{extension}"
    destination = PROFILE_STORAGE_DIR / filename
    destination.write_bytes(contents)
    old_path = user.profile_photo_path
    user.profile_photo_path = f"profile-photos/{filename}"
    db.commit()
    db.refresh(user)

    if old_path and str(old_path).startswith("profile-photos/"):
        old_file = BASE_DIR / "storage" / str(old_path)
        if old_file.is_file() and old_file != destination:
            old_file.unlink()

    return user


@router.get("", response_model=list[UserResponse])
def list_users(
    db: Session = Depends(get_db),
    current_user: User = Depends(require_admin)
):
    result = db.execute(select(User).order_by(User.id.desc()))
    return result.scalars().all()


@router.get("/{user_id}", response_model=UserResponse)
def get_user(
    user_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_admin)
):
    user = db.get(User, user_id)
    if not user:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")
    return user


@router.post("", response_model=UserResponse, status_code=201)
def create_user(
    payload: UserCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_admin)
):
    existing = db.execute(
        select(User).where(User.email == payload.email)
    ).scalar_one_or_none()

    if existing:
        raise HTTPException(status_code=400, detail="El correo ya existe")

    validate_role(payload.role or "user")

    user = User(
        name=payload.name,
        email=payload.email,
        password=hash_password(payload.password),
        role=payload.role or "user",
    )

    db.add(user)
    db.commit()
    db.refresh(user)
    return user


@router.patch("/{user_id}/role", response_model=UserResponse)
def update_user_role(
    user_id: int,
    payload: RoleUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_admin)
):
    validate_role(payload.role)

    user = get_user_or_404(db, user_id)

    if user.id == ROOT_ADMIN_ID and current_user.id != ROOT_ADMIN_ID:
        raise HTTPException(status_code=403, detail="No autorizado para modificar al administrador principal")

    if current_user.id == user.id and payload.role != "admin":
        raise HTTPException(status_code=400, detail="No puedes quitarte tu propio rol de administrador")

    user.role = payload.role
    db.commit()
    db.refresh(user)
    return user


@router.put("/{user_id}", response_model=UserResponse)
def update_user(
    user_id: int,
    payload: UserUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_admin)
):
    user = get_user_or_404(db, user_id)

    update_data = payload.model_dump(exclude_unset=True)

    if not update_data:
        return user

    if user.id == ROOT_ADMIN_ID and current_user.id != ROOT_ADMIN_ID:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No autorizado para modificar al administrador principal"
        )

    if "email" in update_data:
        new_email = (update_data.get("email") or "").strip()
        if not new_email:
            raise HTTPException(status_code=400, detail="El correo es obligatorio")

        existing = db.execute(
            select(User).where(User.email == new_email, User.id != user_id)
        ).scalar_one_or_none()

        if existing:
            raise HTTPException(status_code=400, detail="El correo ya existe")

        user.email = new_email

    if "name" in update_data:
        new_name = (update_data.get("name") or "").strip()
        if not new_name:
            raise HTTPException(status_code=400, detail="El nombre es obligatorio")
        user.name = new_name

    if "role" in update_data and update_data.get("role") is not None:
        new_role = update_data.get("role")
        validate_role(new_role)

        if current_user.id == user.id and new_role != "admin":
            raise HTTPException(
                status_code=400,
                detail="No puedes quitarte tu propio rol de administrador"
            )

        user.role = new_role

    if "password" in update_data:
        new_password = (update_data.get("password") or "").strip()
        if new_password:
            user.password = hash_password(new_password)

    db.commit()
    db.refresh(user)
    return user


@router.delete("/{user_id}", status_code=status.HTTP_200_OK)
def delete_user(
    user_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_admin)
):
    user = get_user_or_404(db, user_id)

    if user.id == ROOT_ADMIN_ID:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No autorizado para eliminar al administrador principal"
        )

    if current_user.id == user.id:
        raise HTTPException(
            status_code=status.HTTP_400_BAD_REQUEST,
            detail="No puedes eliminar tu propio usuario"
        )

    db.delete(user)
    db.commit()

    return {
        "message": "Usuario eliminado correctamente",
        "id": user_id,
    }
