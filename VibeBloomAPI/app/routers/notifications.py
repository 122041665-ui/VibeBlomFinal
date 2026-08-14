from fastapi import APIRouter, Depends, HTTPException
from sqlalchemy import text
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.security import get_current_user
from app.models.user import User

router = APIRouter(prefix="/notifications", tags=["Notifications"])


@router.get("")
def my_notifications(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    rows = db.execute(text("""
        SELECT id, type, title, body, url, data, read_at, created_at
        FROM user_notifications
        WHERE user_id = :user_id
        ORDER BY created_at DESC, id DESC
    """), {"user_id": current_user.id}).mappings().all()
    return [dict(row) for row in rows]


@router.patch("/{notification_id}/read")
def read_notification(
    notification_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    result = db.execute(text("""
        UPDATE user_notifications
        SET read_at = COALESCE(read_at, NOW()), updated_at = NOW()
        WHERE id = :notification_id AND user_id = :user_id
    """), {"notification_id": notification_id, "user_id": current_user.id})
    if result.rowcount == 0:
        db.rollback()
        raise HTTPException(status_code=404, detail="Notificación no encontrada")
    db.commit()
    return {"message": "Notificación marcada como leída"}


@router.post("/read-all")
def read_all_notifications(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    db.execute(text("""
        UPDATE user_notifications
        SET read_at = COALESCE(read_at, NOW()), updated_at = NOW()
        WHERE user_id = :user_id
    """), {"user_id": current_user.id})
    db.commit()
    return {"message": "Notificaciones marcadas como leídas"}
