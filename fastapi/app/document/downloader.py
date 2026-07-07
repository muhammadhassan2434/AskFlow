from pathlib import Path
from tempfile import NamedTemporaryFile

import httpx

from app.core.config import settings
from app.exceptions.document import (
    DocumentDownloadError,
    DocumentNotFoundError,
)
from app.models.document_source import DocumentSource
from app.models.download_result import DownloadResult


class DocumentDownloader:
    """
    Downloads a document to a temporary location.

    Responsibilities:
    - Download the document
    - Handle HTTP errors
    - Save to a temporary file

    It does NOT:
    - Validate documents
    - Parse documents
    - Normalize text
    """

    def download(
        self,
        source: DocumentSource,
    ) -> DownloadResult:

        if not source.file_url:
            raise DocumentDownloadError(
                "Document URL is required."
            )

        try:
            
            with httpx.stream(
                "GET",
                source.file_url,
                timeout=settings.DOCUMENT_DOWNLOAD_TIMEOUT,
                follow_redirects=True,
            ) as response:

                if response.status_code == 404:
                    raise DocumentNotFoundError(
                        "Document not found."
                    )

                response.raise_for_status()

                content_type = response.headers.get(
                    "Content-Type"
                )

                content_length = response.headers.get(
                    "Content-Length"
                )

                if content_length is not None:
                    content_length = int(content_length)

                with NamedTemporaryFile(delete=False) as tmp:

                    for chunk in response.iter_bytes():
                        tmp.write(chunk)

                    return DownloadResult(
                        source=source,
                        local_path=Path(tmp.name),
                        content_type=content_type,
                        content_length=content_length,
                    )

        except httpx.TimeoutException as exc:

            raise DocumentDownloadError(
                "Document download timed out."
            ) from exc

        except httpx.HTTPError as exc:

            raise DocumentDownloadError(
                f"Failed to download document: {exc}"
            ) from exc