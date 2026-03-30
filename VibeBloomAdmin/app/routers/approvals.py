from flask import Blueprint, render_template, session, redirect, url_for, flash, request
from app.services.api_client import api_get, api_post

approvals_bp = Blueprint("approvals", __name__, url_prefix="/approvals")


EMPTY_APPROVALS_CONTEXT = {
    "approvals": [],
    "total_pending": 0,
    "approved_today": 0,
    "rejected_today": 0,
}


def staff_only():
    user = session.get("user", {})
    if not session.get("access_token"):
        return False
    return user.get("role") in ["admin", "moderator"]


def ensure_staff_session():
    if not session.get("access_token"):
        return redirect(url_for("auth.login"))

    if not staff_only():
        flash("No autorizado", "error")
        return redirect(url_for("dashboard.dashboard"))

    return None


def render_empty_approvals(error_message=None):
    if error_message:
        flash(error_message, "error")
    return render_template("approvals.html", **EMPTY_APPROVALS_CONTEXT)


def handle_api_auth_errors(response):
    if response.status_code == 401:
        flash("Sesión expirada", "error")
        session.clear()
        return redirect(url_for("auth.login"))

    if response.status_code == 403:
        flash("No autorizado", "error")
        return redirect(url_for("dashboard.dashboard"))

    return None


def get_error_detail(response, default_message):
    try:
        payload = response.json()
        if isinstance(payload, dict):
            detail = payload.get("detail") or payload.get("message")
            if isinstance(detail, str) and detail.strip():
                return detail.strip()
    except Exception:
        pass
    return default_message


def redirect_to_approvals_with_error(message):
    flash(message, "error")
    return redirect(url_for("approvals.list_approvals"))


def handle_action_response(response, success_message):
    auth_error = handle_api_auth_errors(response)
    if auth_error:
        return auth_error

    if response.status_code == 404:
        flash("Solicitud no encontrada", "warning")
        return redirect(url_for("approvals.list_approvals"))

    if response.status_code != 200:
        flash(get_error_detail(response, "No se pudo completar la acción"), "error")
        return redirect(url_for("approvals.list_approvals"))

    flash(success_message, "success")
    return redirect(url_for("approvals.list_approvals"))


@approvals_bp.route("/", methods=["GET"])
def list_approvals():
    access_guard = ensure_staff_session()
    if access_guard:
        return access_guard

    try:
        response = api_get("/approvals")
    except Exception:
        return render_empty_approvals("No se pudo conectar con la API central")

    auth_error = handle_api_auth_errors(response)
    if auth_error:
        return auth_error

    if response.status_code != 200:
        return render_empty_approvals("No se pudieron cargar las aprobaciones")

    try:
        data = response.json()
    except Exception:
        return render_empty_approvals("La respuesta de aprobaciones no es válida")

    approvals = data.get("approvals", [])
    stats = data.get("stats", {}) if isinstance(data, dict) else {}

    return render_template(
        "approvals.html",
        approvals=approvals,
        total_pending=stats.get("pending", 0),
        approved_today=stats.get("approved", 0),
        rejected_today=stats.get("rejected", 0),
    )


@approvals_bp.route("/<int:place_id>", methods=["GET"])
def approval_detail(place_id):
    access_guard = ensure_staff_session()
    if access_guard:
        return access_guard

    try:
        response = api_get(f"/approvals/{place_id}")
    except Exception:
        flash("No se pudo conectar con la API central", "error")
        return redirect(url_for("approvals.list_approvals"))

    auth_error = handle_api_auth_errors(response)
    if auth_error:
        return auth_error

    if response.status_code == 404:
        flash("Solicitud no encontrada", "warning")
        return redirect(url_for("approvals.list_approvals"))

    if response.status_code != 200:
        flash(get_error_detail(response, "No se pudo cargar el detalle de la solicitud"), "error")
        return redirect(url_for("approvals.list_approvals"))

    try:
        approval = response.json()
    except Exception:
        flash("La respuesta del detalle no es válida", "error")
        return redirect(url_for("approvals.list_approvals"))

    return render_template("approval_detail.html", approval=approval)


@approvals_bp.route("/approve/<int:place_id>", methods=["POST"])
def approve_place(place_id):
    access_guard = ensure_staff_session()
    if access_guard:
        return access_guard

    try:
        response = api_post(f"/approvals/{place_id}/approve", {})
    except Exception:
        return redirect_to_approvals_with_error("No se pudo conectar con la API central")

    auth_error = handle_api_auth_errors(response)
    if auth_error:
        return auth_error

    if response.status_code == 404:
        flash("Solicitud no encontrada", "warning")
        return redirect(url_for("approvals.list_approvals"))

    if response.status_code != 200:
        flash(get_error_detail(response, "No se pudo aprobar la solicitud"), "error")
        return redirect(url_for("approvals.list_approvals"))

    flash("Solicitud aprobada correctamente", "success")
    return redirect(url_for("approvals.list_approvals"))


@approvals_bp.route("/reject/<int:place_id>", methods=["POST"])
def reject_place(place_id):
    access_guard = ensure_staff_session()
    if access_guard:
        return access_guard

    reason = (request.form.get("reason") or "").strip()
    payload = {"reason": reason} if reason else {}

    try:
        response = api_post(f"/approvals/{place_id}/reject", payload)
    except Exception:
        return redirect_to_approvals_with_error("No se pudo conectar con la API central")

    auth_error = handle_api_auth_errors(response)
    if auth_error:
        return auth_error

    if response.status_code == 404:
        flash("Solicitud no encontrada", "warning")
        return redirect(url_for("approvals.list_approvals"))

    if response.status_code != 200:
        flash(get_error_detail(response, "No se pudo rechazar la solicitud"), "error")
        return redirect(url_for("approvals.list_approvals"))

    flash("Solicitud rechazada correctamente", "success")
    return redirect(url_for("approvals.list_approvals"))