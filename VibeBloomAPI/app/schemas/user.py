from pydantic import BaseModel, EmailStr, Field
from typing import Optional


class UserBase(BaseModel):
    name: str = Field(min_length=1, max_length=255)
    email: EmailStr
    role: Optional[str] = "user"


class UserCreate(UserBase):
    password: str = Field(min_length=8, max_length=255)


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
