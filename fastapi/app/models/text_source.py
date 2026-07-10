from pydantic import BaseModel, ConfigDict


class TextSource(BaseModel):
    """
    Represents a manual text source coming from Laravel.
    """

    model_config = ConfigDict(extra="forbid")

    id: int

    bot_id: int

    workspace_id: int

    title: str

    content: str

    status: str