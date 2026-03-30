from pathlib import Path
from uuid import uuid4

from fastapi import APIRouter, Depends, HTTPException, Form, File, UploadFile
from sqlalchemy.orm import Session, selectinload
from sqlalchemy import select
from typing import Optional, List
from datetime import date
import shutil

from app.core.database import get_db
from app.core.security import get_current_user
from app.models.memory import Memory
from app.models.memory_photo import MemoryPhoto
from app.models.user import User
from app.schemas.memory import MemoryResponse

router = APIRouter(prefix="/memories", tags=["Memories"])

BASE_DIR = Path(__file__).resolve().parents[2]
STORAGE_DIR = BASE_DIR / "storage" / "memories"
STORAGE_DIR.mkdir(parents=True, exist_ok=True)


def save_memory_photo(file: UploadFile) -> str:
    ext = Path(file.filename or "").suffix.lower()
    if ext not in [".jpg", ".jpeg", ".png", ".webp"]:
        raise HTTPException(status_code=422, detail="Formato de imagen no permitido")

    filename = f"{uuid4().hex}{ext}"
    relative_path = f"memories/{filename}"
    absolute_path = STORAGE_DIR / filename

    with absolute_path.open("wb") as buffer:
        shutil.copyfileobj(file.file, buffer)

    return relative_path


@router.get("", response_model=list[MemoryResponse])
def my_memories(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    result = db.execute(
        select(Memory)
        .options(selectinload(Memory.photos))
        .where(Memory.user_id == current_user.id)
        .order_by(Memory.id.desc())
    )
    return result.scalars().all()


@router.get("/{memory_id}", response_model=MemoryResponse)
def get_memory(
    memory_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    result = db.execute(
        select(Memory)
        .options(selectinload(Memory.photos))
        .where(Memory.id == memory_id)
    )
    memory = result.scalar_one_or_none()

    if not memory:
        raise HTTPException(status_code=404, detail="Memoria no encontrada")

    if memory.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="No autorizado")

    return memory


@router.post("", response_model=MemoryResponse, status_code=201)
def create_memory(
    title: str = Form(...),
    description: Optional[str] = Form(None),
    memory_date: Optional[date] = Form(None),
    location: Optional[str] = Form(None),
    photos: Optional[List[UploadFile]] = File(None),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    memory = Memory(
        user_id=current_user.id,
        title=title,
        description=description,
        memory_date=memory_date,
        location=location,
    )

    db.add(memory)
    db.commit()
    db.refresh(memory)

    if photos:
        if len(photos) > 3:
            raise HTTPException(status_code=422, detail="Solo se permiten hasta 3 fotos")

        for photo in photos:
            path = save_memory_photo(photo)
            db.add(MemoryPhoto(
                memory_id=memory.id,
                path=path,
            ))

        db.commit()

    result = db.execute(
        select(Memory)
        .options(selectinload(Memory.photos))
        .where(Memory.id == memory.id)
    )
    created_memory = result.scalar_one_or_none()

    if not created_memory:
        raise HTTPException(status_code=500, detail="No se pudo recuperar la memoria creada")

    return created_memory


@router.put("/{memory_id}", response_model=MemoryResponse)
def update_memory(
    memory_id: int,
    title: Optional[str] = Form(None),
    description: Optional[str] = Form(None),
    memory_date: Optional[date] = Form(None),
    location: Optional[str] = Form(None),
    photos: Optional[List[UploadFile]] = File(None),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    result = db.execute(
        select(Memory)
        .options(selectinload(Memory.photos))
        .where(Memory.id == memory_id)
    )
    memory = result.scalar_one_or_none()

    if not memory:
        raise HTTPException(status_code=404, detail="Memoria no encontrada")

    if memory.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="No autorizado")

    if title is not None:
        memory.title = title
    if description is not None:
        memory.description = description
    if memory_date is not None:
        memory.memory_date = memory_date
    if location is not None:
        memory.location = location

    db.commit()
    db.refresh(memory)

    if photos:
        if len(photos) > 3:
            raise HTTPException(status_code=422, detail="Solo se permiten hasta 3 fotos")

        for photo in list(memory.photos):
            old_path = photo.path
            if old_path:
                old_file = BASE_DIR / "storage" / old_path
                if old_file.exists():
                    old_file.unlink(missing_ok=True)
            db.delete(photo)

        db.commit()

        for photo in photos:
            path = save_memory_photo(photo)
            db.add(MemoryPhoto(
                memory_id=memory.id,
                path=path,
            ))

        db.commit()

    result = db.execute(
        select(Memory)
        .options(selectinload(Memory.photos))
        .where(Memory.id == memory_id)
    )
    updated_memory = result.scalar_one_or_none()

    if not updated_memory:
        raise HTTPException(status_code=500, detail="No se pudo recuperar la memoria actualizada")

    return updated_memory


@router.delete("/{memory_id}")
def delete_memory(
    memory_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    result = db.execute(
        select(Memory)
        .options(selectinload(Memory.photos))
        .where(Memory.id == memory_id)
    )
    memory = result.scalar_one_or_none()

    if not memory:
        raise HTTPException(status_code=404, detail="Memoria no encontrada")

    if memory.user_id != current_user.id:
        raise HTTPException(status_code=403, detail="No autorizado")

    for photo in memory.photos:
        if photo.path:
            file_path = BASE_DIR / "storage" / photo.path
            if file_path.exists():
                file_path.unlink(missing_ok=True)

    db.delete(memory)
    db.commit()

    return {"message": "Memoria eliminada correctamente"}