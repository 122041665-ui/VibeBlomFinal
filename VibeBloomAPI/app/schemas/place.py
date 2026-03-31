from datetime import datetime
from typing import Optional

from pydantic import BaseModel, Field, model_validator


class UserMiniResponse(BaseModel):
    id: int
    name: str
    email: Optional[str] = None
    profile_photo_url: Optional[str] = None
    created_at: Optional[datetime] = None

    class Config:
        from_attributes = True


class ReviewReplyResponse(BaseModel):
    id: int
    user_id: int
    review_id: int
    body: str
    created_at: Optional[datetime] = None
    user: Optional[UserMiniResponse] = None

    class Config:
        from_attributes = True


class ReviewDetailResponse(BaseModel):
    id: int
    user_id: int
    place_id: int
    body: str
    created_at: Optional[datetime] = None
    user: Optional[UserMiniResponse] = None
    replies: list[ReviewReplyResponse] = Field(default_factory=list)

    class Config:
        from_attributes = True


class PlaceBase(BaseModel):
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
    photos: Optional[str] = None
    description: Optional[str] = None
    price_range: Optional[float] = None

    @model_validator(mode="after")
    def sync_price_fields(self):
        if self.price_range is None:
            self.price_range = self.price
        return self


class PlaceCreate(PlaceBase):
    @model_validator(mode="before")
    @classmethod
    def map_price_fields(cls, values):
        if isinstance(values, dict):
            if values.get("price") is None and values.get("price_range") is not None:
                values["price"] = values.get("price_range")
            if values.get("price_range") is None and values.get("price") is not None:
                values["price_range"] = values.get("price")
        return values


class PlaceUpdate(BaseModel):
    name: Optional[str] = None
    city: Optional[str] = None
    type: Optional[str] = None
    rating: Optional[int] = None
    address: Optional[str] = None
    reference: Optional[str] = None
    lat: Optional[float] = None
    lng: Optional[float] = None
    price: Optional[float] = None
    price_range: Optional[float] = None
    photo: Optional[str] = None
    photos: Optional[str] = None
    description: Optional[str] = None

    @model_validator(mode="before")
    @classmethod
    def map_price_fields(cls, values):
        if isinstance(values, dict):
            if values.get("price") is None and values.get("price_range") is not None:
                values["price"] = values.get("price_range")
            if values.get("price_range") is None and values.get("price") is not None:
                values["price_range"] = values.get("price")
        return values


class PlaceMineResponse(PlaceBase):
    id: int
    user_id: Optional[int] = None
    photo_url: Optional[str] = None

    class Config:
        from_attributes = True


class PlaceResponse(PlaceBase):
    id: int
    user_id: Optional[int] = None
    photo_url: Optional[str] = None
    user: Optional[UserMiniResponse] = None
    reviews: list[ReviewDetailResponse] = Field(default_factory=list)

    class Config:
        from_attributes = True