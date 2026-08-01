from pydantic import BaseModel, Field
from typing import Optional
from datetime import datetime


class FavoriteToggle(BaseModel):
    place_id: int = Field(gt=0)


class FavoritePlaceResponse(BaseModel):
    id: int
    user_id: int
    name: str
    city: str
    type: Optional[str] = None
    rating: Optional[int] = 0
    address: Optional[str] = None
    reference: Optional[str] = None
    lat: Optional[float] = None
    lng: Optional[float] = None
    price: float
    photo: Optional[str] = None
    photos: Optional[list[str]] = None
    photo_url: Optional[str] = None
    photos_urls: list[str] = Field(default_factory=list)
    description: Optional[str] = None

    class Config:
        from_attributes = True


class FavoriteResponse(BaseModel):
    id: int
    user_id: int
    place_id: int
    created_at: Optional[datetime] = None
    updated_at: Optional[datetime] = None
    place: Optional[FavoritePlaceResponse] = None

    class Config:
        from_attributes = True
