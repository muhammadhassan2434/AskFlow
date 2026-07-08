from typing import Any

from pydantic import BaseModel, ConfigDict, Field


class NormalizedText(BaseModel):
    """
    Result returned by the text normalizer.

    This is the canonical cleaned text before
    chunking begins.
    """

    model_config = ConfigDict(extra="forbid")

    text: str

    metadata: dict[str, Any] = Field(default_factory=dict)