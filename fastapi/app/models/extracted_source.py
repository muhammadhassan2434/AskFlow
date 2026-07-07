from typing import Any

from pydantic import BaseModel, ConfigDict, Field


class ExtractedSource(BaseModel):
    """
    Unified output returned by every loader.

    Every source (PDF, DOCX, TXT, Website, Text, etc.)
    must eventually become this object.
    """

    model_config = ConfigDict(extra="forbid")

    source_id: int

    bot_id: int

    workspace_id: int

    source_type: str

    title: str

    text: str

    metadata: dict[str, Any] = Field(default_factory=dict)