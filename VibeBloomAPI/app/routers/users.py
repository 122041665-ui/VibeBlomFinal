import os
from pathlib import Path
from typing import Optional
from uuid import uuid4

from fastapi import APIRouter, Depends, File, HTTPException, status, UploadFile
from pydantic import BaseModel, EmailStr, Field
from sqlalchemy import select
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.security import hash_password, get_current_user, require_admin
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


@router.get("/community", response_model=list[UserResponse])
def community_users(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    """Return active platform users to any authenticated community member."""
    result = db.execute(select(User).order_by(User.name, User.id))
    return result.scalars().all()


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
