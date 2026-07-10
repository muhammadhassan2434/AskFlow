from typing import Any

from pydantic import BaseModel, ConfigDict, Field


class WebsiteDownload(BaseModel):
    """
    Downloaded HTML page.
    """

    model_config = ConfigDict(extra="forbid")

    source: Any

    html: str

    content_type: str | None = None

    status_code: int

    metadata: dict = Field(default_factory=dict)