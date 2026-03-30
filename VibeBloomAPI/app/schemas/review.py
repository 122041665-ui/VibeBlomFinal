from datetime import datetime
from typing import List, Optional

from pydantic import BaseModel, Field


class ReviewUserResponse(BaseModel):
    id: int
    name: str
    email: Optional[str] = None
    photo: Optional[str] = None
    profile_photo_url: Optional[str] = None

    class Config:
        from_attributes = True


class ReviewPlaceResponse(BaseModel):
    id: int
    name: str
    city: Optional[str] = None
    type: Optional[str] = None
    photo: Optional[str] = None
    photo_url: Optional[str] = None

    class Config:
        from_attributes = True


class ReviewReplyResponse(BaseModel):
    id: int
    review_id: int
    user_id: int
    body: str
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None
    user: Optional[ReviewUserResponse] = None

    class Config:
        from_attributes = True


class ReviewCreate(BaseModel):
    place_id: int
    body: str


class ReviewUpdate(BaseModel):
    body: str


class ReviewResponse(BaseModel):
    id: int
    user_id: int
    place_id: int
    body: str
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None
    user: Optional[ReviewUserResponse] = None
    place: Optional[ReviewPlaceResponse] = None
    replies: List[ReviewReplyResponse] = Field(default_factory=list)

    class Config:
        from_attributes = True