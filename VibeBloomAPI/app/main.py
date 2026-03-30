from pathlib import Path

from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles

from app.core.config import settings
from app.core.database import Base, engine

from app.models import User, Place, Review, Favorite, Memory, ReviewReply, MemoryPhoto

from app.routers.auth import router as auth_router
from app.routers.users import router as users_router
from app.routers.places import router as places_router
from app.routers.admin import router as admin_router
from app.routers.approvals import router as approvals_router
from app.routers.favorites import router as favorites_router
from app.routers.memories import router as memories_router
from app.routers.reports import router as reports_router
from app.routers.reviews import router as reviews_router
from app.routers.dashboard import router as dashboard_router

Base.metadata.create_all(bind=engine)

app = FastAPI(
    title=settings.APP_NAME,
    version="1.0.0",
    description="API central para VibeBloom",
    docs_url="/docs",
    redoc_url="/redoc",
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
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
app.include_router(admin_router)
app.include_router(approvals_router)
app.include_router(favorites_router)
app.include_router(memories_router)
app.include_router(reports_router)
app.include_router(reviews_router)
app.include_router(dashboard_router)


@app.get("/")
def root():
    return {"message": "VibeBloom funcionando correctamente"}   