from typing import Any

from pydantic import BaseModel, ConfigDict, Field


class ParserResult(BaseModel):
    """
    Raw parser output before becoming an ExtractedSource.
    """

    model_config = ConfigDict(extra="forbid")

    text: str

    page_count: int | None = None

    word_count: int = 0

    character_count: int = 0

    metadata: dict[str, Any] = Field(default_factory=dict)