from pydantic import BaseModel, Field


class TrainBotRequest(BaseModel):
    bot_id: int = Field(gt=0)


class ApiResponse(BaseModel):
    success: bool
    message: str