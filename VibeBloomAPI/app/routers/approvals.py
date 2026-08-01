import json
from pathlib import Path
from typing import Any
from uuid import uuid4

from fastapi import APIRouter, Depends, HTTPException, status, Body, File, Form, UploadFile
from sqlalchemy import text
from sqlalchemy.orm import Session

from app.core.security import get_current_user
from app.core.database import get_db
from app.core.security import require_staff
from app.models.user import User

router = APIRouter(prefix="/approvals", tags=["Approvals"])


APPROVAL_TABLE = "place_submissions"
APPROVAL_PHOTOS_TABLE = "place_submission_photos"
PLACES_TABLE = "places"
USERS_TABLE = "users"
BASE_DIR = Path(__file__).resolve().parents[2]
SUBMISSION_STORAGE_DIR = BASE_DIR / "storage" / "place-submissions"

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
    "rejection_reason",
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
    "photos",
    "rating",
    "reference",
    "lat",
    "lng",
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
        "rejection_reason": row.get("rejection_reason"),
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

    photo_col = next(
        (
            candidate
            for candidate in ["photo_path", "path", "url", "photo_url"]
            if column_exists(db, APPROVAL_PHOTOS_TABLE, candidate)
        ),
        None,
    )
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


def attach_approval_photos(db: Session, approval: dict[str, Any]) -> dict[str, Any]:
    photos = get_approval_photos(db, approval.get("id"))
    approval["photos"] = photos
    approval["photo_url"] = photos[0] if photos else None
    return approval


def save_approval_photo(file: UploadFile) -> str:
    extension = Path(file.filename or "").suffix.lower()
    if extension not in [".jpg", ".jpeg", ".png", ".webp"]:
        raise HTTPException(status_code=422, detail="Formato de imagen no permitido")
    if file.content_type not in {"image/jpeg", "image/png", "image/webp"}:
        raise HTTPException(status_code=422, detail="El archivo debe ser una imagen JPG, PNG o WEBP")

    contents = file.file.read()
    if not contents:
        raise HTTPException(status_code=422, detail="La imagen está vacía")
    if len(contents) > 5 * 1024 * 1024:
        raise HTTPException(status_code=422, detail="Cada imagen debe pesar máximo 5 MB")

    SUBMISSION_STORAGE_DIR.mkdir(parents=True, exist_ok=True)

    filename = f"{uuid4().hex}{extension}"
    destination = SUBMISSION_STORAGE_DIR / filename

    with destination.open("wb") as buffer:
        buffer.write(contents)

    return f"place-submissions/{filename}"


def insert_approval_photo(db: Session, approval_id: int, path: str) -> None:
    if not table_exists(db, APPROVAL_PHOTOS_TABLE):
        return

    if not column_exists(db, APPROVAL_PHOTOS_TABLE, "place_submission_id"):
        return

    photo_col = next(
        (
            candidate
            for candidate in ["path", "photo_path", "url", "photo_url"]
            if column_exists(db, APPROVAL_PHOTOS_TABLE, candidate)
        ),
        None,
    )

    if not photo_col:
        return

    cols = ["place_submission_id", photo_col]
    values = [":approval_id", ":path"]
    params = {"approval_id": approval_id, "path": path}

    if column_exists(db, APPROVAL_PHOTOS_TABLE, "created_at"):
        cols.append("created_at")
        values.append("NOW()")

    if column_exists(db, APPROVAL_PHOTOS_TABLE, "updated_at"):
        cols.append("updated_at")
        values.append("NOW()")

    db.execute(
        text(f"""
            INSERT INTO {APPROVAL_PHOTOS_TABLE} ({", ".join(cols)})
            VALUES ({", ".join(values)})
        """),
        params,
    )


@router.post("", status_code=201)
def create_approval(
    name: str = Form(...),
    type: str = Form("Otro"),
    rating: int = Form(0),
    price: float = Form(0),
    city: str = Form(...),
    city_place_id: str | None = Form(None),
    address: str | None = Form(None),
    lat: float | None = Form(None),
    lng: float | None = Form(None),
    description: str | None = Form(None),
    photos: list[UploadFile] = File(default=[]),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    require_table(db, APPROVAL_TABLE)

    name = name.strip()
    city = city.strip()
    if not name or len(name) > 255:
        raise HTTPException(status_code=422, detail="El nombre es obligatorio y debe tener máximo 255 caracteres.")
    if not city or len(city) > 255:
        raise HTTPException(status_code=422, detail="La ciudad es obligatoria y debe tener máximo 255 caracteres.")
    if not 0 <= rating <= 5:
        raise HTTPException(status_code=422, detail="La calificación debe estar entre 0 y 5.")
    if price < 0:
        raise HTTPException(status_code=422, detail="El precio no puede ser negativo.")
    if lat is not None and not -90 <= lat <= 90:
        raise HTTPException(status_code=422, detail="La latitud debe estar entre -90 y 90.")
    if lng is not None and not -180 <= lng <= 180:
        raise HTTPException(status_code=422, detail="La longitud debe estar entre -180 y 180.")
    if address is not None and len(address) > 255:
        raise HTTPException(status_code=422, detail="La dirección debe tener máximo 255 caracteres.")

    if not photos:
        raise HTTPException(
            status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
            detail="Sube al menos una foto.",
        )

    if len(photos) > 3:
        raise HTTPException(
            status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
            detail="Solo puedes subir hasta 3 fotos.",
        )

    cols = get_existing_columns(db, APPROVAL_TABLE, APPROVAL_CANDIDATE_COLUMNS)

    field_map = {
        "user_id": current_user.id,
        "name": name,
        "type": type,
        "rating": rating,
        "price": price,
        "city": city,
        "city_place_id": city_place_id,
        "address": address,
        "lat": lat,
        "lng": lng,
        "description": description,
        "status": "pending",
        "sent_to_flask": True,
        "sent_to_flask_at": None,
    }

    insert_data = {}
    columns_to_insert = []
    values_to_insert = []

    for col in cols:
        if col in ["created_at", "updated_at"]:
            columns_to_insert.append(col)
            values_to_insert.append("NOW()")
        elif col == "sent_to_flask_at":
            columns_to_insert.append(col)
            values_to_insert.append("NOW()")
        elif col in field_map:
            columns_to_insert.append(col)
            values_to_insert.append(f":{col}")
            insert_data[col] = field_map[col]

    try:
        result = db.execute(
            text(f"""
                INSERT INTO {APPROVAL_TABLE} ({", ".join(columns_to_insert)})
                VALUES ({", ".join(values_to_insert)})
            """),
            insert_data,
        )
        approval_id = result.lastrowid

        saved_photos = []
        for photo in photos:
            path = save_approval_photo(photo)
            saved_photos.append(path)
            insert_approval_photo(db, approval_id, path)

        db.commit()

        return {
            "message": "Solicitud enviada correctamente.",
            "approval_id": approval_id,
            "photos": saved_photos,
        }
    except Exception as e:
        db.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail=f"No se pudo crear la solicitud: {str(e)}",
        )


# =========================================================
# LISTADO
# =========================================================
@router.get("")
def list_approvals(
    db: Session = Depends(get_db),
    _: User = Depends(require_staff)
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
    approvals = [
        attach_approval_photos(db, map_approval_row(dict(row._mapping)))
        for row in rows
    ]

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
@router.get("/mine")
def list_my_approvals(
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user),
):
    require_table(db, APPROVAL_TABLE)
    query_sql = build_approval_base_query(
        db,
        "WHERE ps.user_id = :user_id ORDER BY ps.id DESC",
    )
    rows = db.execute(text(query_sql), {"user_id": current_user.id}).fetchall()
    return [
        attach_approval_photos(db, map_approval_row(dict(row._mapping)))
        for row in rows
    ]


@router.get("/{approval_id}")
def get_approval_detail(
    approval_id: int,
    db: Session = Depends(get_db),
    _: User = Depends(require_staff)
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
    admin_user: User = Depends(require_staff)
):
    approval = get_approval_or_404(db, approval_id)
    approval_photos = get_approval_photos(db, approval_id)

    if approval.get("status") == "approved":
        return {
            "message": "La solicitud ya estaba aprobada.",
            "approval_id": approval_id
        }

    require_table(db, PLACES_TABLE)

    place_cols = get_existing_columns(db, PLACES_TABLE, PLACE_CANDIDATE_COLUMNS)

    approval_price = approval.get("price")

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
        "photo": approval_photos[0] if approval_photos else None,
        "photos": approval_photos,
        "rating": approval.get("rating"),
        "lat": approval.get("lat"),
        "lng": approval.get("lng"),
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
            insert_data[col] = json.dumps(value) if col == "photos" else value

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

        if table_exists(db, "user_notifications") and approval.get("user_id"):
            notification_columns = get_existing_columns(db, "user_notifications", [
                "user_id", "actor_id", "type", "title", "body", "url", "data", "created_at", "updated_at"
            ])
            notification_values = {
                "user_id": approval.get("user_id"),
                "actor_id": getattr(admin_user, "id", None),
                "type": "approval_approved",
                "title": "Lugar aprobado exitosamente",
                "body": f"Tu lugar '{approval.get('name') or 'Sin nombre'}' fue aprobado y ya está publicado.",
                "url": f"/mi-perfil#approvals",
                "data": json.dumps({"place_submission_id": approval_id, "place_id": place_id}, ensure_ascii=False),
            }
            insert_columns = []
            insert_values = []
            insert_params = {}
            for column in notification_columns:
                insert_columns.append(column)
                if column in ["created_at", "updated_at"]:
                    insert_values.append("NOW()")
                else:
                    insert_values.append(f":notification_{column}")
                    insert_params[f"notification_{column}"] = notification_values.get(column)

            if "user_id" in insert_columns and "type" in insert_columns:
                db.execute(text(
                    f"INSERT INTO user_notifications ({', '.join(insert_columns)}) "
                    f"VALUES ({', '.join(insert_values)})"
                ), insert_params)

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
    admin_user: User = Depends(require_staff)
):
    approval = get_approval_or_404(db, approval_id)

    reason = (payload.get("reason") or "").strip()
    if len(reason) < 10 or len(reason) > 500:
        raise HTTPException(
            status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
            detail="El motivo del rechazo debe tener entre 10 y 500 caracteres."
        )

    update_fields = []
    params = {"approval_id": approval_id}

    if column_exists(db, APPROVAL_TABLE, "status"):
        update_fields.append("status = 'rejected'")
    if column_exists(db, APPROVAL_TABLE, "rejection_reason"):
        update_fields.append("rejection_reason = :reason")
        params["reason"] = reason

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

        if table_exists(db, "user_notifications") and approval.get("user_id"):
            notification_columns = get_existing_columns(db, "user_notifications", [
                "user_id", "actor_id", "type", "title", "body", "url", "data", "created_at", "updated_at"
            ])
            notification_values = {
                "user_id": approval.get("user_id"),
                "actor_id": getattr(admin_user, "id", None),
                "type": "approval_rejected",
                "title": "Tu publicación fue rechazada",
                "body": f"La solicitud para '{approval.get('name') or 'tu lugar'}' fue rechazada. Motivo: {reason}",
                "url": f"/mis-aprobaciones/{approval_id}",
                "data": json.dumps({"place_submission_id": approval_id, "reason": reason}, ensure_ascii=False),
            }
            insert_columns = []
            insert_values = []
            insert_params = {}
            for column in notification_columns:
                insert_columns.append(column)
                if column in ["created_at", "updated_at"]:
                    insert_values.append("NOW()")
                else:
                    insert_values.append(f":notification_{column}")
                    insert_params[f"notification_{column}"] = notification_values.get(column)

            if "user_id" in insert_columns and "type" in insert_columns:
                db.execute(text(
                    f"INSERT INTO user_notifications ({', '.join(insert_columns)}) "
                    f"VALUES ({', '.join(insert_values)})"
                ), insert_params)
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
