from datetime import datetime
from typing import Optional

from pydantic import BaseModel


class ReviewReplyCreate(BaseModel):
    review_id: int
    body: str


class ReviewReplyUserResponse(BaseModel):
    id: int
    name: str
    email: Optional[str] = None
    role: Optional[str] = "user"
    profile_photo_url: Optional[str] = None

    class Config:
        from_attributes = True


class ReviewReplyResponse(BaseModel):
    id: int
    review_id: int
    user_id: int
    body: str
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None
    user: Optional[ReviewReplyUserResponse] = None

    class Config:
        from_attributes = True