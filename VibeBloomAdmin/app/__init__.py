from flask import Flask, redirect, url_for
from app.config import Config

from app.routers.auth import auth_bp
from app.routers.dashboard import dashboard_bp
from app.routers.approvals import approvals_bp
from app.routers.places import places_bp
from app.routers.users import users_bp
from app.routers.reviews import reviews_bp


def create_app():
    app = Flask(__name__)
    app.config.from_object(Config)

    app.register_blueprint(auth_bp)
    app.register_blueprint(dashboard_bp)
    app.register_blueprint(approvals_bp)
    app.register_blueprint(places_bp)
    app.register_blueprint(users_bp)
    app.register_blueprint(reviews_bp)

    @app.route("/")
    def home():
        return redirect(url_for("auth.login"))

    return app