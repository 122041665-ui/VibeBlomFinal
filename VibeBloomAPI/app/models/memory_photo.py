from sqlalchemy import Column, Integer, String, ForeignKey, DateTime, func
from sqlalchemy.orm import relationship
from app.core.database import Base


class MemoryPhoto(Base):
    __tablename__ = "memory_photos"

    id = Column(Integer, primary_key=True, index=True)
    memory_id = Column(Integer, ForeignKey("memories.id", ondelete="CASCADE"), nullable=False)
    path = Column(String(255), nullable=False)
    created_at = Column(DateTime, nullable=True, server_default=func.now())
    updated_at = Column(DateTime, nullable=True, onupdate=func.now())

    memory = relationship("Memory", back_populates="photos")

    @property
    def url(self):
        if self.path and str(self.path).strip() != "":
            path = str(self.path).lstrip("/")
            return f"http://127.0.0.1:8010/storage/{path}"
        return None