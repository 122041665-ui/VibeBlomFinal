from fastapi import APIRouter, Depends
from sqlalchemy.orm import Session
from sqlalchemy import select, func

from app.core.database import get_db
from app.core.security import require_staff
from app.models.user import User
from app.models.place import Place
from app.models.review import Review
from app.models.favorite import Favorite
from app.models.memory import Memory

router = APIRouter(prefix="/admin", tags=["Admin"])


@router.get("/dashboard")
def admin_dashboard(
    db: Session = Depends(get_db),
    current_user: User = Depends(require_staff)
):
    total_users = db.execute(
        select(func.count()).select_from(User)
    ).scalar() or 0

    total_places = db.execute(
        select(func.count()).select_from(Place)
    ).scalar() or 0

    total_reviews = db.execute(
        select(func.count()).select_from(Review)
    ).scalar() or 0

    total_favorites = db.execute(
        select(func.count()).select_from(Favorite)
    ).scalar() or 0

    total_memories = db.execute(
        select(func.count()).select_from(Memory)
    ).scalar() or 0

    latest_users = db.execute(
        select(User).order_by(User.id.desc()).limit(5)
    ).scalars().all()

    latest_places = db.execute(
        select(Place).order_by(Place.id.desc()).limit(5)
    ).scalars().all()

    latest_reviews = db.execute(
        select(Review).order_by(Review.id.desc()).limit(5)
    ).scalars().all()

    latest_memories = db.execute(
        select(Memory).order_by(Memory.id.desc()).limit(5)
    ).scalars().all()

    return {
        "message": "Dashboard admin de VibeBloom API",
        "current_user": {
            "id": current_user.id,
            "name": current_user.name,
            "email": current_user.email,
            "role": current_user.role
        },
        "stats": {
            "users": total_users,
            "places": total_places,
            "reviews": total_reviews,
            "favorites": total_favorites,
            "memories": total_memories
        },
        "recent": {
            "users": [
                {
                    "id": user.id,
                    "name": user.name,
                    "email": user.email,
                    "role": user.role
                }
                for user in latest_users
            ],
            "places": [
                {
                    "id": place.id,
                    "name": place.name,
                    "city": place.city,
                    "type": place.type,
                    "price": float(place.price) if place.price is not None else None
                }
                for place in latest_places
            ],
            "reviews": [
                {
                    "id": review.id,
                    "user_id": review.user_id,
                    "place_id": review.place_id,
                    "body": review.body
                }
                for review in latest_reviews
            ],
            "memories": [
                {
                    "id": memory.id,
                    "user_id": memory.user_id,
                    "title": memory.title,
                    "memory_date": memory.memory_date.isoformat() if memory.memory_date else None,
                    "location": memory.location
                }
                for memory in latest_memories
            ]
        }
    }