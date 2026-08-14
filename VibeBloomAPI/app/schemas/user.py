import re

from pydantic import BaseModel, EmailStr, Field, field_validator
from typing import Optional


class UserBase(BaseModel):
    name: str = Field(min_length=1, max_length=255)
    email: EmailStr
    role: Optional[str] = "user"
    profile_is_public: bool = True


class UserCreate(UserBase):
    password: str = Field(min_length=8, max_length=255)

    @field_validator("name")
    @classmethod
    def validate_name(cls, value: str) -> str:
        value = value.strip()
        if not value:
            raise ValueError("El nombre es obligatorio.")
        return value

    @field_validator("password")
    @classmethod
    def validate_password(cls, value: str) -> str:
        if not re.search(r"[A-Z]", value):
            raise ValueError("La contraseña debe incluir al menos una mayúscula.")
        if not re.search(r"[a-z]", value):
            raise ValueError("La contraseña debe incluir al menos una minúscula.")
        if not re.search(r"\d", value):
            raise ValueError("La contraseña debe incluir al menos un número.")
        return value


class UserLogin(BaseModel):
    email: EmailStr
    password: str


class UserResponse(UserBase):
    id: int
    profile_photo_url: Optional[str] = None

    class Config:
        from_attributes = True


class TokenResponse(BaseModel):
    access_token: str
    token_type: str
    user: UserResponse
