from sqlalchemy import Column, Integer, String, Text, DECIMAL, ForeignKey, DateTime, func
from sqlalchemy.orm import relationship
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
    photos = Column(Text, nullable=True)
    description = Column(Text, nullable=True)
    created_at = Column(DateTime, nullable=False, server_default=func.now())
    updated_at = Column(DateTime, nullable=True, onupdate=func.now())

    user = relationship("User", back_populates="places")
    reviews = relationship("Review", back_populates="place", cascade="all, delete-orphan")
    favorites = relationship("Favorite", back_populates="place", cascade="all, delete-orphan")

    @property
    def photo_url(self):
        if self.photo and str(self.photo).strip() != "":
            path = str(self.photo).lstrip("/")
            return f"/storage/{path}"
        return None