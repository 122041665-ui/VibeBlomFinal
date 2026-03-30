from sqlalchemy import Column, Integer, Text, String, ForeignKey, DateTime, Date, func
from sqlalchemy.orm import relationship
from app.core.database import Base


class Memory(Base):
    __tablename__ = "memories"

    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, ForeignKey("users.id"), nullable=False)
    title = Column(String(120), nullable=False)
    description = Column(Text, nullable=True)
    memory_date = Column(Date, nullable=True)
    location = Column(String(120), nullable=True)
    created_at = Column(DateTime, nullable=True, server_default=func.now())
    updated_at = Column(DateTime, nullable=True, onupdate=func.now())

    user = relationship("User", back_populates="memories")
    photos = relationship("MemoryPhoto", back_populates="memory", cascade="all, delete-orphan")