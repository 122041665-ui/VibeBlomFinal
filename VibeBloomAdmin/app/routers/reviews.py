from datetime import datetime

from flask import Blueprint, render_template, request, session, redirect, url_for, flash
from app.services.api_client import api_delete, api_get, api_put

reviews_bp = Blueprint("reviews", __name__, url_prefix="/reviews")


def _format_review_datetime(value):
    if not value:
        return "—"

    if isinstance(value, datetime):
        return value.strftime("%d/%m/%Y %H:%M")

    value_str = str(value).strip()
    if not value_str:
        return "—"

    normalized = value_str.replace("Z", "+00:00")

    try:
        parsed = datetime.fromisoformat(normalized)
        return parsed.strftime("%d/%m/%Y %H:%M")
    except Exception:
        return value_str


def _handle_auth_errors(response):
    if response.status_code == 401:
        flash("Sesión expirada", "error")
        session.clear()
        return redirect(url_for("auth.login"))

    if response.status_code == 403:
        flash("No autorizado", "error")
        return redirect(url_for("dashboard.dashboard"))

    return None


def _normalize_reviews(raw_reviews):
    normalized = []

    for item in raw_reviews or []:
        review_id = item.get("id")
        user = item.get("user") or {}
        place = item.get("place") or {}

        normalized.append(
            {
                "id": review_id,
                "user_id": item.get("user_id"),
                "place_id": item.get("place_id"),
                "body": item.get("body"),
                "comment": item.get("comment"),
                "rating": item.get("rating"),
                "created_at": _format_review_datetime(item.get("created_at")),
                "updated_at": _format_review_datetime(item.get("updated_at")),
                "created_at_raw": item.get("created_at"),
                "updated_at_raw": item.get("updated_at"),
                "user_name": user.get("name") or "Sin usuario",
                "place_name": place.get("name") or "Sin lugar",
                "edit_url": url_for("reviews.edit_review_view", review_id=review_id) if review_id else "",
                "delete_url": url_for("reviews.delete_review", review_id=review_id) if review_id else "",
            }
        )

    return normalized


@reviews_bp.route("/", methods=["GET"])
def list_reviews():
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    response = api_get("/reviews")

    auth_redirect = _handle_auth_errors(response)
    if auth_redirect:
        return auth_redirect

    if response.status_code != 200:
        flash("No se pudieron cargar las reseñas", "error")
        return render_template("reviews.html", reviews=[])

    reviews = _normalize_reviews(response.json())
    return render_template("reviews.html", reviews=reviews)


@reviews_bp.route("/<int:review_id>/edit", methods=["GET"])
def edit_review_view(review_id):
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    response = api_get(f"/reviews/{review_id}")

    auth_redirect = _handle_auth_errors(response)
    if auth_redirect:
        return auth_redirect

    if response.status_code == 404:
        flash("Reseña no encontrada", "error")
        return redirect(url_for("reviews.list_reviews"))

    if response.status_code != 200:
        flash("No se pudo cargar la reseña", "error")
        return redirect(url_for("reviews.list_reviews"))

    review = response.json()
    return render_template("edit.html", review=review)


@reviews_bp.route("/<int:review_id>/edit", methods=["POST"])
def update_review(review_id):
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    body = (request.form.get("body") or "").strip()

    if not body:
        flash("El contenido de la reseña es obligatorio.", "error")
        return redirect(url_for("reviews.edit_review_view", review_id=review_id))

    response = api_put(f"/reviews/{review_id}", {"body": body})

    auth_redirect = _handle_auth_errors(response)
    if auth_redirect:
        return auth_redirect

    if response.status_code == 404:
        flash("Reseña no encontrada", "error")
        return redirect(url_for("reviews.list_reviews"))

    if response.status_code != 200:
        flash("No se pudo actualizar la reseña", "error")
        return redirect(url_for("reviews.edit_review_view", review_id=review_id))

    flash("Reseña actualizada correctamente", "success")
    return redirect(url_for("reviews.list_reviews"))


@reviews_bp.route("/<int:review_id>/delete", methods=["POST"])
def delete_review(review_id):
    if "access_token" not in session:
        return redirect(url_for("auth.login"))

    response = api_delete(f"/reviews/{review_id}")

    auth_redirect = _handle_auth_errors(response)
    if auth_redirect:
        return auth_redirect

    if response.status_code == 404:
        flash("Reseña no encontrada", "error")
        return redirect(url_for("reviews.list_reviews"))

    if response.status_code != 200:
        flash("No se pudo eliminar la reseña", "error")
        return redirect(url_for("reviews.list_reviews"))

    flash("Reseña eliminada correctamente", "success")
    return redirect(url_for("reviews.list_reviews"))