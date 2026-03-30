from sqlalchemy import Column, Integer, String, DateTime
from sqlalchemy.orm import relationship
from app.core.database import Base


class User(Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), nullable=False)
    email = Column(String(255), unique=True, nullable=False, index=True)
    email_verified_at = Column(DateTime, nullable=True)
    password = Column(String(255), nullable=False)
    remember_token = Column(String(100), nullable=True)
    current_team_id = Column(Integer, nullable=True)
    profile_photo_path = Column(String(2048), nullable=True)
    created_at = Column(DateTime, nullable=True)
    updated_at = Column(DateTime, nullable=True)
    two_factor_secret = Column(String(255), nullable=True)
    two_factor_recovery_codes = Column(String(255), nullable=True)
    two_factor_confirmed_at = Column(DateTime, nullable=True)
    role = Column(String(50), nullable=False, default="user")

    places = relationship("Place", back_populates="user")
    reviews = relationship("Review", back_populates="user")
    favorites = relationship("Favorite", back_populates="user")
    review_replies = relationship("ReviewReply", back_populates="user")
    memories = relationship("Memory", back_populates="user", cascade="all, delete-orphan")
    
    @property
    def profile_photo_url(self):
        if self.profile_photo_path and str(self.profile_photo_path).strip() != "":
            path = str(self.profile_photo_path).lstrip("/")
            return f"/storage/{path}"
        return None