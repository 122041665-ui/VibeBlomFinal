from pydantic import BaseModel
from typing import Optional
from datetime import date, datetime


class MemoryPhotoResponse(BaseModel):
    id: int
    path: str
    url: Optional[str] = None

    class Config:
        from_attributes = True


class MemoryResponse(BaseModel):
    id: int
    user_id: int
    title: str
    description: Optional[str] = None
    memory_date: Optional[date] = None
    location: Optional[str] = None
    photos: list[MemoryPhotoResponse] = []
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None

    class Config:
        from_attributes = True