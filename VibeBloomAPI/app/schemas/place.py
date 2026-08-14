from datetime import datetime
from typing import Optional

from pydantic import BaseModel, Field, field_validator, model_validator

from app.services.content_moderator import basic_content_violation


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
    name: str = Field(min_length=1, max_length=255)
    city: str = Field(min_length=1, max_length=255)
    type: Optional[str] = Field(default=None, max_length=255)
    rating: Optional[int] = Field(default=0, ge=0, le=5)
    address: Optional[str] = Field(default=None, max_length=255)
    reference: Optional[str] = Field(default=None, max_length=500)
    lat: Optional[float] = Field(default=None, ge=-90, le=90)
    lng: Optional[float] = Field(default=None, ge=-180, le=180)
    price: float = Field(ge=0)
    photo: Optional[str] = None
    photo_url: Optional[str] = None
    photos: Optional[list[str]] = None
    photos_urls: list[str] = Field(default_factory=list)
    description: Optional[str] = None
    price_range: Optional[float] = Field(default=None, ge=0)

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
    name: Optional[str] = Field(default=None, min_length=1, max_length=255)
    city: Optional[str] = Field(default=None, min_length=1, max_length=255)
    type: Optional[str] = Field(default=None, max_length=255)
    rating: Optional[int] = Field(default=None, ge=0, le=5)
    address: Optional[str] = Field(default=None, max_length=255)
    reference: Optional[str] = Field(default=None, max_length=500)
    lat: Optional[float] = Field(default=None, ge=-90, le=90)
    lng: Optional[float] = Field(default=None, ge=-180, le=180)
    price: Optional[float] = Field(default=None, ge=0)
    price_range: Optional[float] = Field(default=None, ge=0)
    photo: Optional[str] = None
    photos: Optional[list[str]] = None
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

    class Config:
        from_attributes = True


class PlaceResponse(PlaceBase):
    id: int
    user_id: Optional[int] = None
    user: Optional[UserMiniResponse] = None
    reviews: list[ReviewDetailResponse] = Field(default_factory=list)

    @field_validator("reviews", mode="before")
    @classmethod
    def hide_invalid_historical_reviews(cls, value):
        return [
            review for review in (value or [])
            if not basic_content_violation(
                review.get("body", "") if isinstance(review, dict) else getattr(review, "body", "")
            )
        ]

    class Config:
        from_attributes = True
