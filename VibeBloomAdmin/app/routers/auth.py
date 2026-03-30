from flask import Blueprint, render_template, request, redirect, url_for, flash, session
from app.services.api_client import api_post

auth_bp = Blueprint("auth", __name__)


@auth_bp.route("/login", methods=["GET", "POST"])
def login():
    if request.method == "POST":
        email = request.form.get("email")
        password = request.form.get("password")

        try:
            response = api_post("/auth/login", {
                "email": email,
                "password": password
            })
        except Exception:
            flash("No se pudo conectar con la API central", "error")
            return redirect(url_for("auth.login"))

        if response.status_code != 200:
            flash("Credenciales inválidas", "error")
            return redirect(url_for("auth.login"))

        data = response.json()
        user = data.get("user", {})
        token = data.get("access_token")

        if user.get("role") not in ["admin", "moderator"]:
            flash("No tienes acceso al panel admin", "error")
            return redirect(url_for("auth.login"))

        session["access_token"] = token
        session["user"] = user

        return redirect(url_for("dashboard.dashboard"))

    return render_template("login.html")


@auth_bp.route("/logout", methods=["POST"])
def logout():
    session.clear()
    return redirect(url_for("auth.login"))