from pydantic import BaseModel, ConfigDict


class Workspace(BaseModel):
    """
    Represents a workspace returned by Laravel.
    """

    model_config = ConfigDict(
        extra="forbid",
        frozen=True,
    )

    id: int

    name: str