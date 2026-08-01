from sqlalchemy import Column, Integer, String, Text, DECIMAL, ForeignKey, DateTime, JSON, func
from sqlalchemy.orm import relationship
from app.core.config import settings
from app.core.database import Base


class Place(Base):
    __tablename__ = "places"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=True)
    name = Column(String(255), nullable=False)
    city = Column(String(255), nullable=False)
    type = Column(String(255), nullable=True)
    rating = Column(Integer, nullable=True, default=0)
    address = Column(String(255), nullable=True)
    reference = Column(String(255), nullable=True)
    lat = Column(DECIMAL(10, 7), nullable=True)
    lng = Column(DECIMAL(10, 7), nullable=True)
    price = Column(DECIMAL(10, 2), nullable=False, default=0)
    photo = Column(String(255), nullable=True)
    photos = Column(JSON, nullable=True)
    description = Column(Text, nullable=True)
    created_at = Column(DateTime, nullable=False, server_default=func.now())
    updated_at = Column(DateTime, nullable=True, onupdate=func.now())

    user = relationship("User", back_populates="places")
    reviews = relationship("Review", back_populates="place", cascade="all, delete-orphan")
    favorites = relationship("Favorite", back_populates="place", cascade="all, delete-orphan")

    @property
    def photo_url(self):
        if self.photo and str(self.photo).strip() != "":
            return self._storage_url(str(self.photo))
        return None

    @property
    def photos_urls(self):
        values = self.photos if isinstance(self.photos, list) else []
        return [self._storage_url(str(path)) for path in values if str(path).strip()]

    @staticmethod
    def _storage_url(path: str):
        path = path.strip()
        if path.startswith(("http://", "https://", "//", "data:")):
            return path
        path = path.lstrip("/")
        if path.startswith("storage/"):
            path = path[len("storage/"):]
        return f"{settings.API_PUBLIC_URL.rstrip('/')}/storage/{path}"
