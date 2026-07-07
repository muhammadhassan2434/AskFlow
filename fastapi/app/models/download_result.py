from pathlib import Path
from typing import Any

from pydantic import BaseModel, ConfigDict, Field

from app.models.document_source import DocumentSource


class DownloadResult(BaseModel):
    """
    Result produced after downloading a document.
    """

    model_config = ConfigDict(
        arbitrary_types_allowed=True,
        extra="forbid",
    )

    source: DocumentSource

    local_path: Path

    content_type: str | None = None

    content_length: int | None = None

    metadata: dict[str, Any] = Field(default_factory=dict)