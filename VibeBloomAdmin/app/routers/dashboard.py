from urllib.parse import urlencode

from flask import Blueprint, render_template, session, redirect, url_for, flash, jsonify, request
from app.services.api_client import api_get

dashboard_bp = Blueprint("dashboard", __name__, url_prefix="/dashboard")


DEFAULT_REPORT_OPTIONS = {
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

DEFAULT_REPORT_HEADERS = [
    "id",
    "nombre",
    "accion",
    "realizado_por",
    "puesto",
    "fecha",
    "estado",
]


def safe_api_json(path: str, default=None, params=None):
    if default is None:
        default = {}

    try:
        response = api_get(path, params=params)
    except Exception:
        return default, None, "connection_error"

    if response.status_code == 401:
        return default, response, "unauthorized"

    if response.status_code == 403:
        return default, response, "forbidden"

    if response.status_code != 200:
        return default, response, "http_error"

    try:
        return response.json(), response, None
    except Exception:
        return default, response, "invalid_json"


def build_dashboard_context(raw_data):
    raw_data = raw_data or {}

    stats = raw_data.get("stats") or {}
    recent = raw_data.get("recent") or {}
    current_user = raw_data.get("current_user") or {}
    recent_activity = raw_data.get("recent_activity") or []

    return {
        "stats": {
            "users": stats.get("users", 0),
            "places": stats.get("places", 0),
            "reviews": stats.get("reviews", 0),
            "favorites": stats.get("favorites", 0),
            "approvals": stats.get("approvals", 0),
        },
        "current_user": {
            "id": current_user.get("id"),
            "name": current_user.get("name", "Usuario"),
            "email": current_user.get("email", "Sin correo"),
            "role": current_user.get("role", "staff"),
        },
        "recent": {
            "users": recent.get("users") or [],
            "places": recent.get("places") or [],
            "reviews": recent.get("reviews") or [],
        },
        "recent_activity": recent_activity,
        "places_by_type": raw_data.get("places_by_type") or [],
        "users_monthly": raw_data.get("users_monthly") or [],
        "places_monthly": raw_data.get("places_monthly") or [],
        "report_options": raw_data.get("report_options") or DEFAULT_REPORT_OPTIONS,
    }


def get_report_filters():
    return {
        "module": request.args.get("module", "places"),
        "scope": request.args.get("scope", "all"),
        "start_date": request.args.get("start_date", ""),
        "end_date": request.args.get("end_date", ""),
    }


def get_module_label(module_value: str) -> str:
    mapping = {
        "places": "Lugares",
        "reviews": "Reseñas",
        "users": "Usuarios",
        "approvals": "Aprobaciones",
    }
    return mapping.get(module_value, "Reporte")


def get_scope_label(module_value: str, scope_value: str, report_options: dict) -> str:
    options = report_options.get(module_value) or []
    for item in options:
        if item.get("value") == scope_value:
            return item.get("label", scope_value)
    return scope_value


@dashboard_bp.route("/")
def dashboard():
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    raw_data, response, error = safe_api_json("/admin/dashboard", default={})

    if error == "connection_error":
        flash("No se pudo conectar con la API central", "error")
        return render_template("dashboard.html", data=build_dashboard_context({}))

    if error == "unauthorized":
        flash("Sesión expirada", "error")
        session.clear()
        return redirect(url_for("auth.login"))

    if error == "forbidden":
        flash("No autorizado", "error")
        return redirect(url_for("auth.login"))

    if error in ("http_error", "invalid_json"):
        flash("No se pudo cargar el dashboard", "error")
        return render_template("dashboard.html", data=build_dashboard_context({}))

    return render_template("dashboard.html", data=build_dashboard_context(raw_data))


@dashboard_bp.route("/report-data")
def report_data():
    if "access_token" not in session:
        return jsonify({"error": "unauthorized"}), 401

    filters = get_report_filters()

    raw_data, response, error = safe_api_json(
        "/admin/dashboard/report-data",
        default={},
        params=filters,
    )

    if error == "unauthorized":
        session.clear()
        return jsonify({"error": "unauthorized"}), 401

    if error in ("forbidden", "connection_error", "http_error", "invalid_json"):
        return jsonify({
            "headers": DEFAULT_REPORT_HEADERS,
            "rows": [],
            "total": 0,
            "module_label": get_module_label(filters["module"]),
            "scope_label": get_scope_label(filters["module"], filters["scope"], DEFAULT_REPORT_OPTIONS),
        }), 200

    report_options = raw_data.get("report_options") or DEFAULT_REPORT_OPTIONS

    return jsonify({
        "headers": raw_data.get("headers") or DEFAULT_REPORT_HEADERS,
        "rows": raw_data.get("rows") or [],
        "total": raw_data.get("total", 0),
        "module_label": raw_data.get("module_label") or get_module_label(filters["module"]),
        "scope_label": raw_data.get("scope_label") or get_scope_label(filters["module"], filters["scope"], report_options),
    })


def build_export_query():
    filters = get_report_filters()
    return urlencode(filters)


@dashboard_bp.route("/export/pdf")
def export_pdf():
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    query_string = build_export_query()
    return redirect(f"/api/admin/dashboard/export/pdf?{query_string}")


@dashboard_bp.route("/export/xls")
def export_xls():
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    query_string = build_export_query()
    return redirect(f"/api/admin/dashboard/export/xls?{query_string}")