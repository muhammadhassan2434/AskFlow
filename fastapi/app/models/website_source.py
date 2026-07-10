from pydantic import BaseModel, ConfigDict


class WebsiteSource(BaseModel):
    """
    Represents a website source received from Laravel.
    """

    model_config = ConfigDict(extra="forbid")

    id: int

    bot_id: int

    workspace_id: int

    title: str

    url: str

    status: str