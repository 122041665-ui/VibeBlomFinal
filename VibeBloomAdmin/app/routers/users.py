from flask import Blueprint, render_template, session, redirect, url_for, flash, request
from app.services.api_client import api_delete, api_get, api_patch, api_put

users_bp = Blueprint("users", __name__, url_prefix="/users")


VALID_ROLES = ["user", "moderator", "admin"]


def admin_only():
    user = session.get("user", {})
    if not session.get("access_token"):
        return False
    return user.get("role") == "admin"


def _auth_required_redirect():
    if not session.get("access_token"):
        return redirect(url_for("auth.login"))

    if not admin_only():
        flash("No autorizado", "error")
        return redirect(url_for("dashboard.dashboard"))

    return None


def _handle_common_response_errors(response, *, not_found_message=None, generic_message=None, redirect_endpoint="users.list_users"):
    if response.status_code == 401:
        flash("Sesión expirada", "error")
        session.clear()
        return redirect(url_for("auth.login"))

    if response.status_code == 403:
        flash("No autorizado", "error")
        return redirect(url_for("dashboard.dashboard"))

    if response.status_code == 404 and not_found_message:
        flash(not_found_message, "warning")
        return redirect(url_for(redirect_endpoint))

    if generic_message and response.status_code >= 400:
        try:
            detail = response.json().get("detail", generic_message)
        except Exception:
            detail = generic_message
        flash(detail, "error")
        return redirect(url_for(redirect_endpoint))

    return None


def _normalize_users(raw_users):
    normalized = []

    for item in raw_users or []:
        user_id = item.get("id")
        normalized.append(
            {
                "id": user_id,
                "name": item.get("name"),
                "email": item.get("email"),
                "role": item.get("role"),
                "profile_photo_url": item.get("profile_photo_url"),
                "photo": item.get("photo"),
                "edit_url": url_for("users.edit_user_view", user_id=user_id) if user_id else "",
                "delete_url": url_for("users.delete_user", user_id=user_id) if user_id else "",
            }
        )

    return normalized


@users_bp.route("/", methods=["GET"])
def list_users():
    auth_redirect = _auth_required_redirect()
    if auth_redirect:
        return auth_redirect

    try:
        response = api_get("/users")
    except Exception:
        flash("No se pudo conectar con la API central", "error")
        return render_template("users.html", users=[])

    error_redirect = _handle_common_response_errors(
        response,
        generic_message="No se pudo cargar la lista de usuarios",
    )
    if error_redirect:
        if response.status_code >= 400 and response.status_code not in [401, 403]:
            return render_template("users.html", users=[])
        return error_redirect

    users = _normalize_users(response.json())
    return render_template("users.html", users=users)


@users_bp.route("/<int:user_id>", methods=["GET"])
def show_user(user_id):
    auth_redirect = _auth_required_redirect()
    if auth_redirect:
        return auth_redirect

    try:
        response = api_get(f"/users/{user_id}")
    except Exception:
        flash("No se pudo conectar con la API central", "error")
        return redirect(url_for("users.list_users"))

    error_redirect = _handle_common_response_errors(
        response,
        not_found_message="Usuario no encontrado",
        generic_message="No se pudo cargar el usuario",
    )
    if error_redirect:
        return error_redirect

    user_data = response.json()
    return render_template("users_detail.html", user=user_data)


@users_bp.route("/<int:user_id>/edit", methods=["GET"])
def edit_user_view(user_id):
    auth_redirect = _auth_required_redirect()
    if auth_redirect:
        return auth_redirect

    try:
        response = api_get(f"/users/{user_id}")
    except Exception:
        flash("No se pudo conectar con la API central", "error")
        return redirect(url_for("users.list_users"))

    error_redirect = _handle_common_response_errors(
        response,
        not_found_message="Usuario no encontrado",
        generic_message="No se pudo cargar el usuario",
    )
    if error_redirect:
        return error_redirect

    user_data = response.json()
    return render_template("users_edit.html", user=user_data)


@users_bp.route("/<int:user_id>/edit", methods=["POST"])
def update_user(user_id):
    auth_redirect = _auth_required_redirect()
    if auth_redirect:
        return auth_redirect

    name = (request.form.get("name") or "").strip()
    email = (request.form.get("email") or "").strip()
    role = (request.form.get("role") or "").strip()
    password = (request.form.get("password") or "").strip()

    if not name:
        flash("El nombre es obligatorio", "warning")
        return redirect(url_for("users.edit_user_view", user_id=user_id))

    if not email:
        flash("El correo es obligatorio", "warning")
        return redirect(url_for("users.edit_user_view", user_id=user_id))

    if role and role not in VALID_ROLES:
        flash("Rol inválido", "warning")
        return redirect(url_for("users.edit_user_view", user_id=user_id))

    payload = {
        "name": name,
        "email": email,
    }

    if role:
        payload["role"] = role

    if password:
        payload["password"] = password

    try:
        response = api_put(f"/users/{user_id}", payload)
    except Exception:
        flash("No se pudo conectar con la API central", "error")
        return redirect(url_for("users.edit_user_view", user_id=user_id))

    error_redirect = _handle_common_response_errors(
        response,
        not_found_message="Usuario no encontrado",
        generic_message="No se pudo actualizar el usuario",
        redirect_endpoint="users.edit_user_view",
    )
    if error_redirect:
        if response.status_code in [401, 403]:
            return error_redirect
        return redirect(url_for("users.edit_user_view", user_id=user_id))

    flash("Usuario actualizado correctamente", "success")
    return redirect(url_for("users.list_users"))


@users_bp.route("/<int:user_id>/delete", methods=["POST"])
def delete_user(user_id):
    auth_redirect = _auth_required_redirect()
    if auth_redirect:
        return auth_redirect

    try:
        response = api_delete(f"/users/{user_id}")
    except Exception:
        flash("No se pudo conectar con la API central", "error")
        return redirect(url_for("users.list_users"))

    error_redirect = _handle_common_response_errors(
        response,
        not_found_message="Usuario no encontrado",
        generic_message="No se pudo eliminar el usuario",
    )
    if error_redirect:
        return error_redirect

    flash("Usuario eliminado correctamente", "success")
    return redirect(url_for("users.list_users"))


@users_bp.route("/<int:user_id>/role", methods=["POST"])
def update_user_role(user_id):
    auth_redirect = _auth_required_redirect()
    if auth_redirect:
        return auth_redirect

    role = (request.form.get("role") or "").strip()

    if role not in VALID_ROLES:
        flash("Rol inválido", "warning")
        return redirect(url_for("users.list_users"))

    try:
        response = api_patch(f"/users/{user_id}/role", {"role": role})
    except Exception:
        flash("No se pudo conectar con la API central", "error")
        return redirect(url_for("users.list_users"))

    error_redirect = _handle_common_response_errors(
        response,
        not_found_message="Usuario no encontrado",
        generic_message="No se pudo actualizar el rol",
    )
    if error_redirect:
        return error_redirect

    flash("Rol actualizado correctamente", "success")
    return redirect(url_for("users.list_users"))