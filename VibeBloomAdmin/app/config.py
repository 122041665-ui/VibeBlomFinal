import os
from dotenv import load_dotenv

load_dotenv()

class Config:
    SECRET_KEY = os.getenv("SECRET_KEY", "dev_key")
    FASTAPI_URL = os.getenv("FASTAPI_URL", "http://127.0.0.1:8010")
    SESSION_COOKIE_HTTPONLY = True
    SESSION_COOKIE_SAMESITE = "Lax"
    SESSION_COOKIE_SECURE = os.getenv("FLASK_ENV") == "production"

    if os.getenv("FLASK_ENV") == "production" and (
        len(SECRET_KEY) < 32 or SECRET_KEY == "dev_key"
    ):
        raise RuntimeError(
            "SECRET_KEY debe ser una clave aleatoria de al menos 32 caracteres en producción."
        )
