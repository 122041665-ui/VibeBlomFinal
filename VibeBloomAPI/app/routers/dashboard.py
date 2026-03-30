from datetime import datetime

from fastapi import APIRouter, Depends, Query
from sqlalchemy import func, select, inspect, text
from sqlalchemy.orm import Session

from app.core.database import get_db
from app.core.security import require_admin
from app.models.user import User
from app.models.place import Place
from app.models.review import Review

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


@router.get("")
def get_admin_dashboard(
    db: Session = Depends(get_db),
    current_user: User = Depends(require_admin),
):
    users_count = db.scalar(select(func.count()).select_from(User)) or 0
    places_count = db.scalar(select(func.count()).select_from(Place)) or 0
    reviews_count = db.scalar(select(func.count()).select_from(Review)) or 0

    favorites_count = 0
    approvals_count = 0

    if has_model_attr(Place, "status"):
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
        "report_options": REPORT_OPTIONS,
    }


@router.get("/report-data")
def get_dashboard_report_data(
    module: str = Query("places"),
    scope: str = Query("all"),
    start_date: str = Query(""),
    end_date: str = Query(""),
    db: Session = Depends(get_db),
    current_user: User = Depends(require_admin),
):
    if module == "approvals":
        params = {}
        where_clauses = [
            "section = 'places'",
            "action IN ('approve', 'reject')",
        ]

        if scope == "approved":
            where_clauses.append("action = 'approve'")
        elif scope == "rejected":
            where_clauses.append("action = 'reject'")

        if start_date:
            where_clauses.append("DATE(created_at) >= :start_date")
            params["start_date"] = start_date

        if end_date:
            where_clauses.append("DATE(created_at) <= :end_date")
            params["end_date"] = end_date

        sql = text(f"""
            SELECT
                id,
                section,
                action,
                entity_id,
                entity_name,
                description,
                created_at
            FROM audit_logs
            WHERE {' AND '.join(where_clauses)}
            ORDER BY created_at DESC
        """)

        rows = db.execute(sql, params).mappings().all()

        data_rows = [
            {
                "id": row["entity_id"] or row["id"],
                "nombre": row["entity_name"] or "Sin nombre",
                "accion": row["action"] or "-",
                "realizado_por": "Sistema",
                "puesto": "places",
                "fecha": str(row["created_at"]) if row["created_at"] else "",
                "estado": "aprobado" if row["action"] == "approve" else "rechazado",
            }
            for row in rows
        ]

        return {
            "headers": REPORT_HEADERS,
            "rows": data_rows,
            "total": len(data_rows),
            "module_label": get_module_label(module),
            "scope_label": get_scope_label(module, scope),
        }

    if module == "places" and scope == "current":
        query = select(Place).order_by(Place.id.desc())
        query = apply_date_filters(query, Place, start_date, end_date)

        if has_model_attr(Place, "status"):
            query = query.where(getattr(Place, "status") == "approved")

        rows = db.execute(query).scalars().all()

        data_rows = [
            {
                "id": item.id,
                "nombre": getattr(item, "name", "Sin nombre"),
                "accion": "current",
                "realizado_por": f"Usuario {getattr(item, 'user_id', '-')}",
                "puesto": "user",
                "fecha": str(getattr(item, "created_at", "") or ""),
                "estado": getattr(item, "status", "activo") if has_model_attr(Place, "status") else "activo",
            }
            for item in rows
        ]

        return {
            "headers": REPORT_HEADERS,
            "rows": data_rows,
            "total": len(data_rows),
            "module_label": get_module_label(module),
            "scope_label": get_scope_label(module, scope),
        }

    if module in ["places", "users", "reviews"]:
        params = {"section": module}
        where_clauses = ["section = :section"]

        if scope == "created":
            where_clauses.append("action IN ('create', 'created', 'store')")
        elif scope == "updated":
            where_clauses.append("action IN ('update', 'updated', 'edit')")
        elif scope == "deleted":
            where_clauses.append("action IN ('delete', 'deleted')")
        elif scope == "all":
            pass

        if start_date:
            where_clauses.append("DATE(created_at) >= :start_date")
            params["start_date"] = start_date

        if end_date:
            where_clauses.append("DATE(created_at) <= :end_date")
            params["end_date"] = end_date

        sql = text(f"""
            SELECT
                id,
                section,
                action,
                entity_id,
                entity_name,
                description,
                created_at
            FROM audit_logs
            WHERE {' AND '.join(where_clauses)}
            ORDER BY created_at DESC
        """)

        rows = db.execute(sql, params).mappings().all()

        data_rows = []
        for row in rows:
            action_value = row["action"] or "-"
            estado = "registrado"

            if action_value in ["delete", "deleted"]:
                estado = "eliminado"
            elif action_value in ["update", "updated", "edit"]:
                estado = "modificado"
            elif action_value in ["create", "created", "store"]:
                estado = "creado"

            data_rows.append({
                "id": row["entity_id"] or row["id"],
                "nombre": row["entity_name"] or "Sin nombre",
                "accion": action_value,
                "realizado_por": "Sistema",
                "puesto": row["section"] or "-",
                "fecha": str(row["created_at"]) if row["created_at"] else "",
                "estado": estado,
            })

        return {
            "headers": REPORT_HEADERS,
            "rows": data_rows,
            "total": len(data_rows),
            "module_label": get_module_label(module),
            "scope_label": get_scope_label(module, scope),
        }

    return {
        "headers": REPORT_HEADERS,
        "rows": [],
        "total": 0,
        "module_label": get_module_label(module),
        "scope_label": get_scope_label(module, scope),
    }