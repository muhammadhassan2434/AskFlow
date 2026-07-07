from pydantic import BaseModel, ConfigDict

from app.models.bot_source import BotSource
from app.models.workspace import Workspace


class Bot(BaseModel):
    """
    Represents a bot returned by Laravel.
    """

    model_config = ConfigDict(
        extra="forbid",
        frozen=True,
    )

    id: int

    workspace_id: int

    name: str

    description: str | None = None

    system_prompt: str | None = None

    status: str

    workspace: Workspace

    sources: list[BotSource]