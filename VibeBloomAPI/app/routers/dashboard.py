from datetime import date, datetime

from fastapi import APIRouter, Depends, HTTPException, Query
from sqlalchemy import func, select, inspect, text
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.security import require_staff
from app.models.user import User
from app.models.place import Place
from app.models.review import Review
from app.models.favorite import Favorite

router = APIRouter(prefix="/admin/dashboard", tags=["Admin Dashboard"])


REPORT_OPTIONS = {
    "places": [
        {"value": "all", "label": "Todas las acciones"},
        {"value": "created", "label": "Agregados"},
        {"value": "updated", "label": "Modificaciones"},
        {"value": "deleted", "label": "Eliminados"},
        {"value": "current", "label": "Solo lugares actuales"},
    ],
    "reviews": [
        {"value": "all", "label": "Todas las acciones"},
        {"value": "created", "label": "Agregados"},
        {"value": "updated", "label": "Modificaciones"},
        {"value": "deleted", "label": "Eliminados"},
    ],
    "users": [
        {"value": "all", "label": "Todas las acciones"},
        {"value": "created", "label": "Agregados"},
        {"value": "updated", "label": "Modificaciones"},
        {"value": "deleted", "label": "Eliminados"},
    ],
    "approvals": [
        {"value": "all", "label": "Todas las acciones"},
        {"value": "approved", "label": "Aprobadas"},
        {"value": "rejected", "label": "Rechazadas"},
    ],
}

REPORT_HEADERS = ["id", "nombre", "accion", "realizado_por", "puesto", "fecha", "estado"]


def has_model_attr(model, attr_name: str) -> bool:
    try:
        mapper = inspect(model)
        return attr_name in mapper.columns
    except Exception:
        return hasattr(model, attr_name)


def table_exists(db: Session, table_name: str) -> bool:
    row = db.execute(text("""
        SELECT COUNT(*)
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = :table_name
    """), {"table_name": table_name}).scalar()
    return bool(row)


def get_month_label(dt_value):
    if not dt_value:
        return ""
    if isinstance(dt_value, str):
        try:
            dt_value = datetime.fromisoformat(dt_value)
        except Exception:
            return str(dt_value)
    return dt_value.strftime("%b")


def normalize_rows(rows):
    return [dict(row._mapping) for row in rows]


def get_module_label(module: str) -> str:
    labels = {
        "places": "Lugares",
        "users": "Usuarios",
        "reviews": "Reseñas",
        "approvals": "Aprobaciones",
    }
    return labels.get(module, "Reporte")


def get_scope_label(module: str, scope: str) -> str:
    return next(
        (item["label"] for item in REPORT_OPTIONS.get(module, []) if item["value"] == scope),
        scope,
    )


def apply_date_filters(query, model, start_date: str = "", end_date: str = ""):
    if not has_model_attr(model, "created_at"):
        return query

    created_col = getattr(model, "created_at")

    if start_date:
        query = query.where(func.date(created_col) >= start_date)

    if end_date:
        query = query.where(func.date(created_col) <= end_date)

    return query


def sql_date_conditions(column: str, start_date: date | None, end_date: date | None, params: dict):
    conditions = []
    if start_date:
        conditions.append(f"DATE({column}) >= :start_date")
        params["start_date"] = start_date
    if end_date:
        conditions.append(f"DATE({column}) <= :end_date")
        params["end_date"] = end_date
    return conditions


def current_report_rows(db: Session, module: str, scope: str, start_date: date | None, end_date: date | None):
    definitions = {
        "places": {
            "table": "places",
            "select": "p.id, p.name AS nombre, p.user_id, p.type AS puesto, p.created_at, p.updated_at",
            "from": "places p",
            "actor": "CONCAT('Usuario ', COALESCE(p.user_id, '-'))",
        },
        "users": {
            "table": "users",
            "select": "u.id, u.name AS nombre, u.id AS user_id, COALESCE(u.role, 'user') AS puesto, u.created_at, u.updated_at",
            "from": "users u",
            "actor": "u.name",
        },
        "reviews": {
            "table": "reviews",
            "select": "r.id, LEFT(r.body, 120) AS nombre, r.user_id, CONCAT('Lugar ', r.place_id) AS puesto, r.created_at, r.updated_at",
            "from": "reviews r",
            "actor": "CONCAT('Usuario ', COALESCE(r.user_id, '-'))",
        },
    }
    definition = definitions[module]
    params = {}
    conditions = []
    date_column = "updated_at" if scope == "updated" else "created_at"
    conditions.extend(sql_date_conditions(date_column, start_date, end_date, params))
    if scope == "updated":
        conditions.append("updated_at IS NOT NULL AND updated_at > created_at")

    where_sql = f"WHERE {' AND '.join(conditions)}" if conditions else ""
    rows = db.execute(text(f"""
        SELECT {definition['select']}, {definition['actor']} AS realizado_por
        FROM {definition['from']}
        {where_sql}
        ORDER BY {date_column} DESC, id DESC
    """), params).mappings().all()

    return [
        {
            "id": row["id"],
            "nombre": row["nombre"] or "Sin nombre",
            "accion": "updated" if scope == "updated" else "created",
            "realizado_por": row["realizado_por"] or "Sistema",
            "puesto": row["puesto"] or "-",
            "fecha": str(row[date_column] or ""),
            "estado": "modificado" if scope == "updated" else "activo",
        }
        for row in rows
    ]


def deleted_report_rows(db: Session, module: str, start_date: date | None, end_date: date | None):
    params = {"section": module}
    conditions = ["section = :section", "action IN ('delete', 'deleted')"]
    conditions.extend(sql_date_conditions("created_at", start_date, end_date, params))
    rows = db.execute(text(f"""
        SELECT id, entity_id, entity_name, section, created_at
        FROM audit_logs
        WHERE {' AND '.join(conditions)}
        ORDER BY created_at DESC, id DESC
    """), params).mappings().all()
    return [
        {
            "id": row["entity_id"] or row["id"],
            "nombre": row["entity_name"] or "Sin nombre",
            "accion": "deleted",
            "realizado_por": "Administrador",
            "puesto": row["section"] or module,
            "fecha": str(row["created_at"] or ""),
            "estado": "eliminado",
        }
        for row in rows
    ]


def approval_report_rows(db: Session, scope: str, start_date: date | None, end_date: date | None):
    params = {}
    conditions = []
    if scope in {"approved", "rejected"}:
        conditions.append("ps.status = :status")
        params["status"] = scope
    date_column = "COALESCE(ps.updated_at, ps.created_at)"
    conditions.extend(sql_date_conditions(date_column, start_date, end_date, params))
    where_sql = f"WHERE {' AND '.join(conditions)}" if conditions else ""
    rows = db.execute(text(f"""
        SELECT ps.id, ps.name, ps.type, ps.status, ps.user_id,
               ps.created_at, ps.updated_at, u.name AS user_name
        FROM place_submissions ps
        LEFT JOIN users u ON u.id = ps.user_id
        {where_sql}
        ORDER BY {date_column} DESC, ps.id DESC
    """), params).mappings().all()
    status_labels = {"pending": "pendiente", "approved": "aprobado", "rejected": "rechazado"}
    return [
        {
            "id": row["id"],
            "nombre": row["name"] or "Sin nombre",
            "accion": row["status"] or "pending",
            "realizado_por": row["user_name"] or f"Usuario {row['user_id'] or '-'}",
            "puesto": row["type"] or "Lugar",
            "fecha": str(row["updated_at"] or row["created_at"] or ""),
            "estado": status_labels.get(row["status"], row["status"] or "pendiente"),
        }
        for row in rows
    ]


def get_recent_activity_from_audit(db: Session, limit: int = 8):
    rows = db.execute(text("""
        SELECT
            id,
            section,
            action,
            entity_id,
            entity_name,
            description,
            created_at
        FROM audit_logs
        ORDER BY created_at DESC
        LIMIT :limit_value
    """), {"limit_value": limit}).mappings().all()

    return [
        {
            "module": row["section"] or "general",
            "title": row["entity_name"] or f"Registro #{row['id']}",
            "detail": row["description"] or "Movimiento registrado",
            "action": row["action"] or "acción",
            "created_at": str(row["created_at"]) if row["created_at"] else "",
        }
        for row in rows
    ]


def get_places_by_type(db: Session):
    if not has_model_attr(Place, "type"):
        return []

    rows = db.execute(
        select(
            func.coalesce(getattr(Place, "type"), "Sin tipo").label("label"),
            func.count().label("total"),
        )
        .group_by(func.coalesce(getattr(Place, "type"), "Sin tipo"))
        .order_by(func.count().desc())
    ).all()

    return normalize_rows(rows)


def get_monthly_counts(db: Session, model):
    if not has_model_attr(model, "created_at"):
        return []

    created_col = getattr(model, "created_at")

    rows = db.execute(
        select(
            func.year(created_col).label("year"),
            func.month(created_col).label("month"),
            func.min(created_col).label("date_ref"),
            func.count().label("total"),
        )
        .group_by(
            func.year(created_col),
            func.month(created_col),
        )
        .order_by(
            func.year(created_col),
            func.month(created_col),
        )
    ).all()

    return [
        {
            "label": get_month_label(row.date_ref),
            "total": row.total,
        }
        for row in rows
    ]


def get_approval_status_counts(db: Session):
    if table_exists(db, "place_submissions"):
        rows = db.execute(text("""
            SELECT
                COALESCE(status, 'pending') AS label,
                COUNT(*) AS total
            FROM place_submissions
            GROUP BY COALESCE(status, 'pending')
            ORDER BY total DESC
        """)).fetchall()
    elif has_model_attr(Place, "status"):
        status_col = getattr(Place, "status")

        rows = db.execute(
            select(
                func.coalesce(status_col, "sin_estado").label("label"),
                func.count().label("total"),
            )
            .where(status_col.in_(["pending", "approved", "rejected"]))
            .group_by(func.coalesce(status_col, "sin_estado"))
            .order_by(func.count().desc())
        ).all()
    else:
        return []

    status_labels = {
        "pending": "Pendientes",
        "approved": "Aprobadas",
        "rejected": "Rechazadas",
        "sin_estado": "Sin estado",
    }

    return [
        {
            "label": status_labels.get(row.label, str(row.label).title()),
            "total": row.total,
        }
        for row in rows
    ]


@router.get("")
def get_admin_dashboard(
    db: Session = Depends(get_db),
    current_user: User = Depends(require_staff),
):
    users_count = db.scalar(select(func.count()).select_from(User)) or 0
    places_count = db.scalar(select(func.count()).select_from(Place)) or 0
    reviews_count = db.scalar(select(func.count()).select_from(Review)) or 0
    favorites_count = db.scalar(select(func.count()).select_from(Favorite)) or 0

    approvals_count = 0

    if table_exists(db, "place_submissions"):
        approvals_count = db.execute(text("""
            SELECT COUNT(*)
            FROM place_submissions
            WHERE COALESCE(status, 'pending') IN ('pending', 'approved', 'rejected')
        """)).scalar() or 0
    elif has_model_attr(Place, "status"):
        status_col = getattr(Place, "status")
        approvals_count = db.scalar(
            select(func.count()).select_from(Place).where(
                status_col.in_(["pending", "approved", "rejected"])
            )
        ) or 0

    recent_users = db.execute(
        select(User).order_by(User.id.desc()).limit(5)
    ).scalars().all()

    recent_places = db.execute(
        select(Place).order_by(Place.id.desc()).limit(5)
    ).scalars().all()

    recent_reviews = db.execute(
        select(Review).order_by(Review.id.desc()).limit(5)
    ).scalars().all()

    return {
        "stats": {
            "users": users_count,
            "places": places_count,
            "reviews": reviews_count,
            "favorites": favorites_count,
            "approvals": approvals_count,
        },
        "current_user": {
            "id": current_user.id,
            "name": current_user.name,
            "email": current_user.email,
            "role": getattr(current_user, "role", "admin"),
        },
        "recent": {
            "users": [
                {
                    "id": user.id,
                    "name": getattr(user, "name", "Sin nombre"),
                    "email": getattr(user, "email", "Sin correo"),
                    "role": getattr(user, "role", "user"),
                }
                for user in recent_users
            ],
            "places": [
                {
                    "id": place.id,
                    "name": getattr(place, "name", "Sin nombre"),
                    "city": getattr(place, "city", "Sin ciudad"),
                    "type": getattr(place, "type", "Lugar"),
                    "price": getattr(place, "price", None) if has_model_attr(Place, "price") else None,
                }
                for place in recent_places
            ],
            "reviews": [
                {
                    "id": review.id,
                    "body": getattr(review, "body", "Sin contenido"),
                    "user_id": getattr(review, "user_id", None),
                    "place_id": getattr(review, "place_id", None),
                }
                for review in recent_reviews
            ],
        },
        "recent_activity": get_recent_activity_from_audit(db, limit=8),
        "places_by_type": get_places_by_type(db),
        "users_monthly": get_monthly_counts(db, User),
        "places_monthly": get_monthly_counts(db, Place),
        "reviews_monthly": get_monthly_counts(db, Review),
        "favorites_monthly": get_monthly_counts(db, Favorite),
        "approvals_by_status": get_approval_status_counts(db),
        "report_options": REPORT_OPTIONS,
    }


@router.get("/report-data")
def get_dashboard_report_data(
    module: str = Query("places"),
    scope: str = Query("all"),
    start_date: date | None = Query(None),
    end_date: date | None = Query(None),
    db: Session = Depends(get_db),
    current_user: User = Depends(require_staff),
):
    if start_date and end_date and start_date > end_date:
        raise HTTPException(
            status_code=422,
            detail="La fecha inicial no puede ser posterior a la fecha final.",
        )

    allowed_scopes = {item["value"] for item in REPORT_OPTIONS.get(module, [])}
    if not allowed_scopes or scope not in allowed_scopes:
        raise HTTPException(status_code=422, detail="La categoría o acción del reporte no es válida.")

    if module == "approvals":
        data_rows = approval_report_rows(db, scope, start_date, end_date)
    elif scope == "deleted":
        data_rows = deleted_report_rows(db, module, start_date, end_date)
    elif scope == "updated":
        data_rows = current_report_rows(db, module, "updated", start_date, end_date)
    elif scope in {"created", "current"}:
        data_rows = current_report_rows(db, module, "created", start_date, end_date)
        if scope == "current":
            for row in data_rows:
                row["accion"] = "current"
                row["estado"] = "activo"
    else:
        data_rows = current_report_rows(db, module, "created", start_date, end_date)
        data_rows.extend(deleted_report_rows(db, module, start_date, end_date))
        data_rows.sort(key=lambda item: item["fecha"], reverse=True)

    return {
        "headers": REPORT_HEADERS,
        "rows": data_rows,
        "total": len(data_rows),
        "module_label": get_module_label(module),
        "scope_label": get_scope_label(module, scope),
    }
