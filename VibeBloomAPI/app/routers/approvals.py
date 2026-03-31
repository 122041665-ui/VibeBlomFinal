from typing import Any

from fastapi import APIRouter, Depends, HTTPException, status, Body
from sqlalchemy import text
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.security import require_admin

router = APIRouter(prefix="/approvals", tags=["Approvals"])


APPROVAL_TABLE = "place_submissions"
APPROVAL_PHOTOS_TABLE = "place_submission_photos"
PLACES_TABLE = "places"
USERS_TABLE = "users"

APPROVAL_CANDIDATE_COLUMNS = [
    "id",
    "user_id",
    "name",
    "type",
    "rating",
    "price",
    "city",
    "city_place_id",
    "address",
    "lat",
    "lng",
    "description",
    "status",
    "sent_to_flask",
    "sent_to_flask_at",
    "created_at",
    "updated_at",
]

PLACE_CANDIDATE_COLUMNS = [
    "name",
    "type",
    "city",
    "address",
    "description",
    "price",
    "price_range",
    "latitude",
    "longitude",
    "photo",
    "user_id",
    "status",
    "created_at",
    "updated_at",
]


# =========================================================
# HELPERS
# =========================================================
def table_exists(db: Session, table_name: str) -> bool:
    query = text("""
        SELECT COUNT(*)
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = :table_name
    """)
    return db.execute(query, {"table_name": table_name}).scalar() > 0



def column_exists(db: Session, table_name: str, column_name: str) -> bool:
    query = text("""
        SELECT COUNT(*)
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = :table_name
          AND column_name = :column_name
    """)
    return db.execute(query, {
        "table_name": table_name,
        "column_name": column_name,
    }).scalar() > 0



def get_existing_columns(db: Session, table_name: str, candidates: list[str]) -> list[str]:
    return [col for col in candidates if column_exists(db, table_name, col)]



def require_table(db: Session, table_name: str) -> None:
    if not table_exists(db, table_name):
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail=f"La tabla {table_name} no existe."
        )



def get_user_select_parts(db: Session) -> list[str]:
    select_parts = []

    join_user = table_exists(db, USERS_TABLE) and column_exists(db, APPROVAL_TABLE, "user_id")
    if not join_user:
        return select_parts

    if column_exists(db, USERS_TABLE, "name"):
        select_parts.append("u.name AS user_name")
    if column_exists(db, USERS_TABLE, "email"):
        select_parts.append("u.email AS user_email")

    return select_parts



def build_approval_base_query(db: Session, where_clause: str = "") -> str:
    cols = get_existing_columns(db, APPROVAL_TABLE, APPROVAL_CANDIDATE_COLUMNS)

    if not cols:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="No se encontraron columnas válidas en place_submissions."
        )

    select_parts = [f"ps.{col}" for col in cols]
    select_parts.extend(get_user_select_parts(db))

    query_sql = f"""
        SELECT {", ".join(select_parts)}
        FROM {APPROVAL_TABLE} ps
    """

    join_user = table_exists(db, USERS_TABLE) and column_exists(db, APPROVAL_TABLE, "user_id")
    if join_user:
        query_sql += " LEFT JOIN users u ON u.id = ps.user_id "

    if where_clause:
        query_sql += f" {where_clause} "

    return query_sql



def map_approval_row(row: dict[str, Any]) -> dict[str, Any]:
    return {
        "id": row.get("id"),
        "user_id": row.get("user_id"),
        "name": row.get("name") or "Solicitud sin nombre",
        "type": row.get("type") or "Sin tipo",
        "city": row.get("city") or "Sin ciudad",
        "address": row.get("address"),
        "description": row.get("description"),
        "price": row.get("price"),
        "rating": row.get("rating"),
        "latitude": row.get("lat"),
        "longitude": row.get("lng"),
        "status": row.get("status") or "pending",
        "sent_to_flask": row.get("sent_to_flask"),
        "sent_to_flask_at": row.get("sent_to_flask_at"),
        "created_at": row.get("created_at"),
        "updated_at": row.get("updated_at"),
        "user": {
            "name": row.get("user_name"),
            "email": row.get("user_email"),
        } if row.get("user_name") or row.get("user_email") else None,
    }



def get_approval_or_404(db: Session, approval_id: int) -> dict[str, Any]:
    require_table(db, APPROVAL_TABLE)

    query_sql = build_approval_base_query(
        db,
        "WHERE ps.id = :approval_id LIMIT 1"
    )

    row = db.execute(text(query_sql), {"approval_id": approval_id}).fetchone()

    if not row:
        raise HTTPException(
            status_code=status.HTTP_404_NOT_FOUND,
            detail="La aprobación no fue encontrada."
        )

    return dict(row._mapping)



def get_approval_photos(db: Session, approval_id: int) -> list[str]:
    if not table_exists(db, APPROVAL_PHOTOS_TABLE):
        return []

    if not column_exists(db, APPROVAL_PHOTOS_TABLE, "place_submission_id"):
        return []

    photo_col = "photo_path" if column_exists(db, APPROVAL_PHOTOS_TABLE, "photo_path") else None
    if not photo_col:
        return []

    photos_query = text(f"""
        SELECT {photo_col}
        FROM {APPROVAL_PHOTOS_TABLE}
        WHERE place_submission_id = :approval_id
        ORDER BY id ASC
    """)

    rows = db.execute(photos_query, {"approval_id": approval_id}).fetchall()
    return [dict(item._mapping)[photo_col] for item in rows]


# =========================================================
# LISTADO
# =========================================================
@router.get("")
def list_approvals(
    db: Session = Depends(get_db),
    _: dict = Depends(require_admin)
):
    require_table(db, APPROVAL_TABLE)

    query_sql = build_approval_base_query(db)

    cols = get_existing_columns(db, APPROVAL_TABLE, APPROVAL_CANDIDATE_COLUMNS)

    if "status" in cols:
        query_sql += """
            ORDER BY
                CASE
                    WHEN ps.status = 'pending' THEN 1
                    WHEN ps.status = 'approved' THEN 2
                    WHEN ps.status = 'rejected' THEN 3
                    ELSE 4
                END,
                ps.id DESC
        """
    else:
        query_sql += " ORDER BY ps.id DESC "

    rows = db.execute(text(query_sql)).fetchall()
    approvals = [map_approval_row(dict(row._mapping)) for row in rows]

    stats = {
        "pending": 0,
        "approved": 0,
        "rejected": 0,
        "total": len(approvals)
    }

    if "status" in cols:
        stats_query = text(f"""
            SELECT
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected,
                COUNT(*) AS total
            FROM {APPROVAL_TABLE}
        """)
        stats_row = db.execute(stats_query).fetchone()
        if stats_row:
            stats = dict(stats_row._mapping)

    return {
        "message": "Listado de aprobaciones",
        "stats": stats,
        "approvals": approvals
    }


# =========================================================
# DETALLE
# =========================================================
@router.get("/{approval_id}")
def get_approval_detail(
    approval_id: int,
    db: Session = Depends(get_db),
    _: dict = Depends(require_admin)
):
    approval_row = get_approval_or_404(db, approval_id)
    approval = map_approval_row(approval_row)
    approval["photos"] = get_approval_photos(db, approval_id)

    return approval


# =========================================================
# APROBAR
# =========================================================
@router.post("/{approval_id}/approve")
def approve_approval(
    approval_id: int,
    db: Session = Depends(get_db),
    admin_user: dict = Depends(require_admin)
):
    approval = get_approval_or_404(db, approval_id)

    if approval.get("status") == "approved":
        return {
            "message": "La solicitud ya estaba aprobada.",
            "approval_id": approval_id
        }

    require_table(db, PLACES_TABLE)

    place_cols = get_existing_columns(db, PLACES_TABLE, PLACE_CANDIDATE_COLUMNS)

    approval_price = approval.get("price")

    # Get first photo from submission photos
    first_photo = None
    submission_photos = get_approval_photos(db, approval_id)
    if submission_photos:
        first_photo = submission_photos[0]

    field_map = {
        "name": approval.get("name"),
        "type": approval.get("type"),
        "city": approval.get("city"),
        "address": approval.get("address"),
        "description": approval.get("description"),
        "price": approval_price,
        "price_range": approval_price,
        "latitude": approval.get("lat"),
        "longitude": approval.get("lng"),
        "photo": first_photo,
        "user_id": approval.get("user_id"),
        "status": "approved",
    }

    insert_data = {}
    columns_to_insert = []
    values_to_insert = []

    for col in place_cols:
        if col in ["created_at", "updated_at"]:
            columns_to_insert.append(col)
            values_to_insert.append("NOW()")
        elif col in field_map:
            value = field_map[col]
            if value is None and col in ["price", "price_range"]:
                continue
            columns_to_insert.append(col)
            values_to_insert.append(f":{col}")
            insert_data[col] = value

    if not columns_to_insert:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="No hay columnas válidas para insertar en places."
        )

    insert_place_sql = f"""
        INSERT INTO {PLACES_TABLE} ({", ".join(columns_to_insert)})
        VALUES ({", ".join(values_to_insert)})
    """

    try:
        result = db.execute(text(insert_place_sql), insert_data)
        place_id = result.lastrowid

        update_fields = []
        update_params = {"approval_id": approval_id}

        if column_exists(db, APPROVAL_TABLE, "status"):
            update_fields.append("status = 'approved'")

        if update_fields:
            update_sql = f"""
                UPDATE {APPROVAL_TABLE}
                SET {", ".join(update_fields)}
                WHERE id = :approval_id
            """
            db.execute(text(update_sql), update_params)

        db.commit()

        return {
            "message": "Solicitud aprobada correctamente.",
            "place_id": place_id,
            "approval_id": approval_id
        }

    except Exception as e:
        db.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al aprobar la solicitud: {str(e)}"
        )


# =========================================================
# RECHAZAR
# =========================================================
@router.post("/{approval_id}/reject")
def reject_approval(
    approval_id: int,
    payload: dict = Body(default={}),
    db: Session = Depends(get_db),
    admin_user: dict = Depends(require_admin)
):
    get_approval_or_404(db, approval_id)

    reason = (payload.get("reason") or "").strip()

    update_fields = []
    params = {"approval_id": approval_id}

    if column_exists(db, APPROVAL_TABLE, "status"):
        update_fields.append("status = 'rejected'")

    if not update_fields:
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="No hay columnas válidas para actualizar en place_submissions."
        )

    try:
        update_sql = f"""
            UPDATE {APPROVAL_TABLE}
            SET {", ".join(update_fields)}
            WHERE id = :approval_id
        """
        db.execute(text(update_sql), params)
        db.commit()

        return {
            "message": "Solicitud rechazada correctamente.",
            "approval_id": approval_id,
            "reason": reason if reason else None
        }

    except Exception as e:
        db.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"Error al rechazar la solicitud: {str(e)}"
        )