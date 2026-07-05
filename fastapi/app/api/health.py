from datetime import datetime, UTC

from fastapi import APIRouter

from app.core.config import settings

router = APIRouter()


@router.get(
    "/health",
    tags=["System"],
    summary="Application Health Check",
)
def health():
    return {
        "success": True,
        "service": settings.APP_NAME,
        "environment": settings.APP_ENV,
        "status": "healthy",
        "timestamp": datetime.now(UTC).isoformat(),
    }