from pydantic import BaseModel, ConfigDict


class BotSource(BaseModel):
    """
    Represents a knowledge source attached to a bot.
    """

    model_config = ConfigDict(
        extra="forbid",
        frozen=True,
    )

    id: int

    type: str

    title: str

    content: str | None = None

    url: str | None = None

    file_name: str | None = None

    file_path: str | None = None

    file_url: str | None = None

    file_type: str | None = None

    file_size: int | None = None

    status: str