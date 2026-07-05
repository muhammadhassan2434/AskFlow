from fastapi import APIRouter, Header, HTTPException, status

from app.core.config import settings
from app.schemas.bot import ApiResponse, TrainBotRequest
from app.services.bot_training import BotTrainingService

router = APIRouter(
    prefix="/bots",
    tags=["Bots"],
)

training_service = BotTrainingService()

@router.post(
    "/train",
    response_model=ApiResponse,
    summary="Start Bot Training",
)
def train_bot(
    payload: TrainBotRequest,
    x_api_key: str = Header(...),
):
    """
    Receives a training request from Laravel.

    At this stage we only validate the request.
    The actual processing will be implemented later.
    """

    if x_api_key != settings.API_KEY:
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Invalid API Key",
        )
    
    training_service.train(payload)

    return ApiResponse(
        success=True,
        message="Bot training started successfully.",
    )