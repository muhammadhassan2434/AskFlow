from typing import Any

from pydantic import BaseModel, ConfigDict, Field


class DocumentChunk(BaseModel):
    """
    Represents a single chunk that will later
    be embedded and stored in the vector database.
    """

    model_config = ConfigDict(extra="forbid")

    bot_id: int

    workspace_id: int

    source_id: int

    source_type: str

    title: str

    chunk_index: int

    text: str

    metadata: dict[str, Any] = Field(default_factory=dict)