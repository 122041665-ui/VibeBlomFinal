import os

from flask import Blueprint, render_template, request, session, redirect, url_for, flash
from app.services.api_client import api_delete, api_get, api_post_files, api_put

places_bp = Blueprint("places", __name__, url_prefix="/places")
ALLOWED_IMAGE_TYPES = {"image/jpeg", "image/png", "image/webp"}
PLACE_TYPES = ["Restaurante", "Cafetería", "Bar", "Antro", "Parque", "Mirador", "Museo", "Plaza", "Centro comercial", "Otro"]
MEXICAN_STATES = [
    "Aguascalientes", "Baja California", "Baja California Sur", "Campeche", "Chiapas", "Chihuahua",
    "Ciudad de México", "Coahuila", "Colima", "Durango", "Estado de México", "Guanajuato", "Guerrero",
    "Hidalgo", "Jalisco", "Michoacán", "Morelos", "Nayarit", "Nuevo León", "Oaxaca", "Puebla", "Querétaro",
    "Quintana Roo", "San Luis Potosí", "Sinaloa", "Sonora", "Tabasco", "Tamaulipas", "Tlaxcala",
    "Veracruz", "Yucatán", "Zacatecas",
]


def _handle_auth_errors(response):
    if response.status_code == 401:
        flash("Sesión expirada", "error")
        session.clear()
        return redirect(url_for("auth.login"))

    if response.status_code == 403:
        flash("No autorizado", "error")
        return redirect(url_for("dashboard.dashboard"))

    return None


def _normalize_asset_url(value):
    if not value:
        return None

    value = str(value).strip()
    if not value:
        return None

    if value.startswith(("http://", "https://", "data:")):
        return value

    public_base = os.getenv("API_PUBLIC_URL", "http://127.0.0.1:8010").rstrip("/")
    path = value if value.startswith("/") else f"/storage/{value.lstrip('/')}"
    return f"{public_base}{path}"


def _normalize_places(raw_places):
    normalized = []

    for item in raw_places or []:
        place_id = item.get("id")
        user = item.get("user") or {}
        photo = _normalize_asset_url(item.get("photo"))
        photo_url = _normalize_asset_url(item.get("photo_url") or item.get("photo"))
        photos_urls = [
            url for url in (_normalize_asset_url(value) for value in (item.get("photos_urls") or item.get("photos") or []))
            if url
        ]

        normalized.append(
            {
                "id": place_id,
                "name": item.get("name"),
                "description": item.get("description"),
                "city": item.get("city"),
                "type": item.get("type"),
                "price_range": item.get("price_range") if item.get("price_range") is not None else item.get("price"),
                "rating": item.get("rating"),
                "photo": photo,
                "photo_url": photo_url,
                "photos_urls": photos_urls,
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
    place["photo_url"] = _normalize_asset_url(place.get("photo_url") or place.get("photo"))
    place["photos_urls"] = [
        url for url in (_normalize_asset_url(value) for value in (place.get("photos_urls") or place.get("photos") or []))
        if url
    ]
    return render_template("places_edit.html", place=place, place_types=PLACE_TYPES, mexican_states=MEXICAN_STATES)


@places_bp.route("/<int:place_id>/edit", methods=["POST"])
def update_place(place_id):
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    name = (request.form.get("name") or "").strip()
    description = (request.form.get("description") or "").strip()
    city = (request.form.get("city") or "").strip()
    place_type = (request.form.get("type") or "").strip()
    price_raw = (request.form.get("price_range") or "").strip()
    photos = [photo for photo in request.files.getlist("photos") if photo and photo.filename]

    if not name:
        flash("El nombre del lugar es obligatorio.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    if not city:
        flash("El estado del lugar es obligatorio.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    if city not in MEXICAN_STATES:
        flash("Selecciona un estado válido de México.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    if place_type not in PLACE_TYPES:
        flash("Selecciona un tipo de lugar válido.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    if not description:
        flash("La descripción del lugar es obligatoria.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    if len(description) < 20 or len(description) > 1000:
        flash("La descripción debe tener entre 20 y 1000 caracteres.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    if not price_raw:
        flash("El precio del lugar es obligatorio.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    if len(photos) > 3:
        flash("Solo puedes subir hasta 3 fotos.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    for photo in photos:
        if photo.mimetype not in ALLOWED_IMAGE_TYPES:
            flash("Las fotos deben ser JPG, PNG o WEBP.", "error")
            return redirect(url_for("places.edit_place_view", place_id=place_id))
        photo.stream.seek(0, 2)
        photo_size = photo.stream.tell()
        photo.stream.seek(0)
        if photo_size > 5 * 1024 * 1024:
            flash("Cada foto debe pesar máximo 5 MB.", "error")
            return redirect(url_for("places.edit_place_view", place_id=place_id))

    try:
        price_value = float(price_raw) if price_raw else None
    except ValueError:
        flash("El precio debe ser una cantidad válida.", "error")
        return redirect(url_for("places.edit_place_view", place_id=place_id))

    if price_value is None or price_value < 0:
        flash("El precio debe ser una cantidad igual o mayor que cero.", "error")
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

    if photos:
        photo_response = api_post_files(
            f"/places/{place_id}/photos",
            [("photos", (photo.filename, photo.stream, photo.mimetype)) for photo in photos],
        )
        auth_redirect = _handle_auth_errors(photo_response)
        if auth_redirect:
            return auth_redirect
        if photo_response.status_code != 200:
            flash("Los datos se guardaron, pero no se pudieron actualizar las fotos.", "error")
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
