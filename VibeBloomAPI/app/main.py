from pathlib import Path

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
from sqlalchemy import text

from app.core.config import settings
from app.core.database import Base, SessionLocal, engine

from app.models import User, Place, Review, Favorite, Memory, ReviewReply, MemoryPhoto

from app.routers.auth import router as auth_router
from app.routers.users import router as users_router
from app.routers.places import router as places_router
from app.routers.approvals import router as approvals_router
from app.routers.favorites import router as favorites_router
from app.routers.memories import router as memories_router
from app.routers.reports import router as reports_router
from app.routers.reviews import router as reviews_router
from app.routers.review_replies import router as review_replies_router
from app.routers.dashboard import router as dashboard_router
from app.routers.notifications import router as notifications_router
from app.routers.mobile import router as mobile_router
from app.routers.ai import router as ai_router

Base.metadata.create_all(bind=engine)

app = FastAPI(
    title=settings.APP_NAME,
    version="1.0.0",
    description="API central para VibeBloom",
    docs_url="/docs" if settings.APP_ENV != "production" else None,
    redoc_url="/redoc" if settings.APP_ENV != "production" else None,
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.CORS_ORIGINS,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

BASE_DIR = Path(__file__).resolve().parent.parent
STORAGE_DIR = BASE_DIR / "storage"

STORAGE_DIR.mkdir(parents=True, exist_ok=True)

app.mount("/storage", StaticFiles(directory=str(STORAGE_DIR)), name="storage")

app.include_router(auth_router)
app.include_router(users_router)
app.include_router(places_router)
app.include_router(approvals_router)
app.include_router(favorites_router)
app.include_router(memories_router)
app.include_router(reports_router)
app.include_router(reviews_router)
app.include_router(review_replies_router)
app.include_router(dashboard_router)
app.include_router(notifications_router)
app.include_router(mobile_router)
app.include_router(ai_router)


@app.get("/")
def root():
    return {"message": "VibeBloom funcionando correctamente"}


@app.get("/health", include_in_schema=False)
def health():
    with SessionLocal() as db:
        db.execute(text("SELECT 1"))
    return {"status": "ok", "database": "ok"}
