from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy.orm import Session, joinedload
from sqlalchemy import select

from app.core.database import get_db
from app.core.security import get_current_user
from app.models.favorite import Favorite
from app.models.place import Place
from app.models.user import User
from app.schemas.favorite import FavoriteToggle, FavoriteResponse

router = APIRouter(prefix="/favorites", tags=["Favorites"])


@router.get("", response_model=list[FavoriteResponse])
def my_favorites(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    result = db.execute(
        select(Favorite)
        .options(joinedload(Favorite.place))
        .where(Favorite.user_id == current_user.id)
        .order_by(Favorite.id.desc())
    )
    return result.scalars().all()


@router.post("/toggle")
def toggle_favorite(
    payload: FavoriteToggle,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    place = db.get(Place, payload.place_id)

    if not place:
        raise HTTPException(status_code=404, detail="Lugar no encontrado")

    favorite = db.execute(
        select(Favorite).where(
            Favorite.user_id == current_user.id,
            Favorite.place_id == payload.place_id
        )
    ).scalar_one_or_none()

    if favorite:
        db.delete(favorite)
        db.commit()
        return {
            "message": "Favorito eliminado",
            "is_favorite": False,
            "place_id": payload.place_id
        }

    new_favorite = Favorite(
        user_id=current_user.id,
        place_id=payload.place_id
    )

    db.add(new_favorite)
    db.commit()
    db.refresh(new_favorite)

    return {
        "message": "Favorito agregado",
        "is_favorite": True,
        "favorite": {
            "id": new_favorite.id,
            "user_id": new_favorite.user_id,
            "place_id": new_favorite.place_id
        }
    }