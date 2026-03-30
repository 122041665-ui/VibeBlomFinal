from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy.orm import Session, joinedload
from sqlalchemy import select

from app.core.database import get_db
from app.core.security import get_current_user
from app.models.review import Review
from app.models.review_reply import ReviewReply
from app.models.user import User
from app.schemas.review_reply import ReviewReplyCreate, ReviewReplyResponse

router = APIRouter(prefix="/review-replies", tags=["Review Replies"])


@router.post("", response_model=ReviewReplyResponse, status_code=status.HTTP_201_CREATED)
def create_review_reply(
    payload: ReviewReplyCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    review = db.get(Review, payload.review_id)

    if not review:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Reseña no encontrada"
        )

    reply = ReviewReply(
        review_id=payload.review_id,
        user_id=current_user.id,
        body=payload.body
    )

    db.add(reply)
    db.commit()
    db.refresh(reply)

    result = db.execute(
        select(ReviewReply)
        .options(joinedload(ReviewReply.user))
        .where(ReviewReply.id == reply.id)
    ).scalar_one()

    return result


@router.delete("/{reply_id}", status_code=status.HTTP_200_OK)
def delete_review_reply(
    reply_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    reply = db.get(ReviewReply, reply_id)

    if not reply:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Respuesta no encontrada"
        )

    if reply.user_id != current_user.id and current_user.role not in ["admin", "moderator"]:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No autorizado"
        )

    db.delete(reply)
    db.commit()

    return {
        "message": "Respuesta eliminada correctamente",
        "id": reply_id
    }