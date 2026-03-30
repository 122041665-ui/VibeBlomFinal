from flask import Blueprint, render_template, request, session, redirect, url_for, flash
from app.services.api_client import api_delete, api_get, api_put

places_bp = Blueprint("places", __name__, url_prefix="/places")


def _handle_auth_errors(response):
    if response.status_code == 401:
        flash("Sesión expirada", "error")
        session.clear()
        return redirect(url_for("auth.login"))

    if response.status_code == 403:
        flash("No autorizado", "error")
        return redirect(url_for("dashboard.dashboard"))

    return None


def _normalize_places(raw_places):
    normalized = []

    for item in raw_places or []:
        place_id = item.get("id")
        user = item.get("user") or {}

        normalized.append(
            {
                "id": place_id,
                "name": item.get("name"),
                "description": item.get("description"),
                "city": item.get("city"),
                "type": item.get("type"),
                "price_range": item.get("price_range") if item.get("price_range") is not None else item.get("price"),
                "rating": item.get("rating"),
                "photo": item.get("photo"),
                "photo_url": item.get("photo_url"),
                "user_id": item.get("user_id"),
                "user_name": user.get("name"),
                "edit_url": url_for("places.edit_place_view", place_id=place_id) if place_id else "",
                "delete_url": url_for("places.delete_place", place_id=place_id) if place_id else "",
            }
        )

    return normalized


@places_bp.route("/", methods=["GET"])
def list_places():
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    response = api_get("/places")

    auth_redirect = _handle_auth_errors(response)
    if auth_redirect:
        return auth_redirect

    if response.status_code != 200:
        flash("No se pudieron cargar los lugares", "error")
        return render_template("places.html", places=[])

    places = _normalize_places(response.json())
    return render_template("places.html", places=places)


@places_bp.route("/<int:place_id>/edit", methods=["GET"])
def edit_place_view(place_id):
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    response = api_get(f"/places/{place_id}")

    auth_redirect = _handle_auth_errors(response)
    if auth_redirect:
        return auth_redirect

    if response.status_code == 404:
        flash("Lugar no encontrado", "error")
        return redirect(url_for("places.list_places"))

    if response.status_code != 200:
        flash("No se pudo cargar el lugar", "error")
        return redirect(url_for("places.list_places"))

    place = response.json()
    return render_template("places_edit.html", place=place)


@places_bp.route("/<int:place_id>/edit", methods=["POST"])
def update_place(place_id):
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    name = (request.form.get("name") or "").strip()
    description = (request.form.get("description") or "").strip()
    city = (request.form.get("city") or "").strip()
    place_type = (request.form.get("type") or "").strip()
    price_raw = (request.form.get("price_range") or "").strip()

    if not name:
        flash("El nombre del lugar es obligatorio.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    try:
        price_value = float(price_raw) if price_raw else None
    except ValueError:
        flash("El precio debe ser una cantidad válida.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    payload = {
        "name": name,
        "description": description,
        "city": city,
        "type": place_type,
        "price_range": price_value,
    }

    response = api_put(f"/places/{place_id}", payload)

    auth_redirect = _handle_auth_errors(response)
    if auth_redirect:
        return auth_redirect

    if response.status_code == 404:
        flash("Lugar no encontrado", "error")
        return redirect(url_for("places.list_places"))

    if response.status_code != 200:
        flash("No se pudo actualizar el lugar", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    flash("Lugar actualizado correctamente", "success")
    return redirect(url_for("places.list_places"))


@places_bp.route("/<int:place_id>/delete", methods=["POST"])
def delete_place(place_id):
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    response = api_delete(f"/places/{place_id}")

    auth_redirect = _handle_auth_errors(response)
    if auth_redirect:
        return auth_redirect

    if response.status_code == 404:
        flash("Lugar no encontrado", "error")
        return redirect(url_for("places.list_places"))

    if response.status_code != 200:
        flash("No se pudo eliminar el lugar", "error")
        return redirect(url_for("places.list_places"))

    flash("Lugar eliminado correctamente", "success")
    return redirect(url_for("places.list_places"))