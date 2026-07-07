from pydantic import BaseModel, ConfigDict


class DocumentSource(BaseModel):
    """
    Represents a document source received from Laravel.

    This model is the single source of truth for the document
    throughout the loading pipeline.
    """

    model_config = ConfigDict(extra="forbid")

    id: int

    bot_id: int

    workspace_id: int

    title: str

    file_name: str

    file_path: str

    file_url: str

    file_type: str

    file_size: int

    status: str