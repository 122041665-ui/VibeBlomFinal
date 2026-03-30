from typing import Optional

from fastapi import APIRouter, Depends, HTTPException, status
from pydantic import BaseModel
from sqlalchemy import select
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.security import hash_password, get_current_user, require_admin
from app.models.user import User
from app.schemas.user import UserCreate, UserResponse

router = APIRouter(prefix="/users", tags=["Users"])

ROOT_ADMIN_ID = 1


class RoleUpdate(BaseModel):
    role: str


class UserUpdate(BaseModel):
    name: Optional[str] = None
    email: Optional[str] = None
    role: Optional[str] = None
    password: Optional[str] = None


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