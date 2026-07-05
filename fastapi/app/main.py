from fastapi import FastAPI

from app.core.config import settings
from app.api.health import router as health_router
from app.api.bots import router as bot_router

app = FastAPI(
    title=settings.APP_NAME,
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc",
)

app.include_router(health_router)
app.include_router(bot_router)