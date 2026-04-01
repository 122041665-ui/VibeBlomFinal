import os
from dotenv import load_dotenv

load_dotenv()

class Config:
    SECRET_KEY = os.getenv("SECRET_KEY", "dev_key")
    FASTAPI_URL = os.getenv("FASTAPI_URL", "http://127.0.0.1:8010")
    LARAVEL_PUBLIC_URL = os.getenv("LARAVEL_PUBLIC_URL", "http://localhost:8000")
