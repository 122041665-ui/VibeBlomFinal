from fastapi import APIRouter, Depends, HTTPException, status
from sqlalchemy import select
from sqlalchemy.orm import Session, joinedload

from app.core.database import get_db
from app.core.security import get_current_user, require_staff
from app.models.place import Place
from app.models.review import Review
from app.models.review_reply import ReviewReply
from app.models.user import User
from app.schemas.review import ReviewCreate, ReviewResponse, ReviewUpdate

router = APIRouter(prefix="/reviews", tags=["Reviews"])


def get_review_base_query():
    return (
        select(Review)
        .options(
            joinedload(Review.user),
            joinedload(Review.place),
            joinedload(Review.replies).joinedload(ReviewReply.user),
        )
    )


def get_review_with_relations(db: Session, review_id: int):
    return (
        db.execute(
            get_review_base_query().where(Review.id == review_id)
        )
        .unique()
        .scalar_one_or_none()
    )


def can_manage_review(current_user: User, review: Review) -> bool:
    user_role = getattr(current_user, "role", None)
    return review.user_id == current_user.id or user_role in ["admin", "moderator"]


@router.get("/mine/list", response_model=list[ReviewResponse])
def my_reviews(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    result = db.execute(
        get_review_base_query()
        .where(Review.user_id == current_user.id)
        .order_by(Review.id.desc())
    )

    return result.unique().scalars().all()


@router.get("/place/{place_id}", response_model=list[ReviewResponse])
def reviews_by_place(
    place_id: int,
    db: Session = Depends(get_db),
):
    result = db.execute(
        get_review_base_query()
        .where(Review.place_id == place_id)
        .order_by(Review.id.desc())
    )

    return result.unique().scalars().all()


@router.get("", response_model=list[ReviewResponse])
def list_reviews(
    db: Session = Depends(get_db),
    current_user: User = Depends(require_staff),
):
    result = db.execute(
        get_review_base_query()
        .order_by(Review.id.desc())
    )

    return result.unique().scalars().all()


@router.get("/{review_id}", response_model=ReviewResponse)
def get_review(
    review_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(require_staff),
):
    review = get_review_with_relations(db, review_id)

    if not review:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Reseña no encontrada",
        )

    return review


@router.post("", response_model=ReviewResponse, status_code=status.HTTP_201_CREATED)
def create_review(
    payload: ReviewCreate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    place = db.get(Place, payload.place_id)

    if not place:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Lugar no encontrado",
        )

    body = (payload.body or "").strip()
    if not body:
        raise HTTPException(
            status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
            detail="El contenido de la reseña no puede estar vacío",
        )

    review = Review(
        user_id=current_user.id,
        place_id=payload.place_id,
        body=body,
    )

    db.add(review)
    db.commit()
    db.refresh(review)

    created_review = get_review_with_relations(db, review.id)

    if not created_review:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="No se pudo recuperar la reseña creada",
        )

    return created_review


@router.put("/{review_id}", response_model=ReviewResponse)
def update_review(
    review_id: int,
    payload: ReviewUpdate,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    review = db.get(Review, review_id)

    if not review:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Reseña no encontrada",
        )

    if not can_manage_review(current_user, review):
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No autorizado",
        )

    body = (payload.body or "").strip()
    if not body:
        raise HTTPException(
            status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
            detail="El contenido de la reseña no puede estar vacío",
        )

    review.body = body

    db.commit()
    db.refresh(review)

    updated_review = get_review_with_relations(db, review.id)

    if not updated_review:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="No se pudo recuperar la reseña actualizada",
        )

    return updated_review


@router.delete("/{review_id}", status_code=status.HTTP_200_OK)
def delete_review(
    review_id: int,
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    review = db.get(Review, review_id)

    if not review:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="Reseña no encontrada",
        )

    if not can_manage_review(current_user, review):
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="No autorizado",
        )

    db.delete(review)
    db.commit()

    return {
        "message": "Reseña eliminada correctamente",
        "id": review_id,
    }