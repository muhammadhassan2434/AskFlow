from pathlib import Path

from app.core.config import settings
from app.exceptions.document import (
    DocumentNotReadableError,
    DocumentTooLargeError,
    EmptyDocumentError,
    InvalidMimeTypeError,
    UnsupportedDocumentTypeError,
)
from app.models.download_result import DownloadResult


class DocumentValidator:
    """
    Validates downloaded documents before parsing.
    """

    def validate(self, download: DownloadResult) -> None:

        self._validate_file_exists(download)

        self._validate_extension(download)

        self._validate_mime(download)

        self._validate_size(download)


    def _validate_file_exists(self,download: DownloadResult) -> None:

        path = download.local_path

        if not path.exists():
            raise DocumentNotReadableError(
                "Downloaded document does not exist."
            )

        if not path.is_file():
            raise DocumentNotReadableError(
                "Downloaded path is not a file."
            )

        if not path.stat().st_size:
            raise EmptyDocumentError(
                "Downloaded document is empty."
            )
        
    def _validate_extension(self,download: DownloadResult) -> None:

            allowed = {
                ext.strip().lower()
                for ext in settings.DOCUMENT_ALLOWED_EXTENSIONS.split(",")
            }

            extension = download.source.file_type.lower()

            if extension not in allowed:

                raise UnsupportedDocumentTypeError(
                    f"Unsupported document type: {extension}"
                )
            
    def _validate_mime(self,download: DownloadResult,) -> None:

        allowed = {
            mime.strip().lower()
            for mime in settings.DOCUMENT_ALLOWED_MIME_TYPES.split(",")
        }

        if download.content_type is None:

            raise InvalidMimeTypeError(
                "Missing Content-Type."
            )

        mime = download.content_type.split(";")[0].strip().lower()

        if mime not in allowed:

            raise InvalidMimeTypeError(
                f"Unsupported MIME type: {mime}"
            )
        
    def _validate_size(self,download: DownloadResult,) -> None:

        size = download.local_path.stat().st_size

        if size == 0:

            raise EmptyDocumentError(
                "Downloaded document is empty."
            )

        if size > settings.DOCUMENT_MAX_FILE_SIZE:

            raise DocumentTooLargeError(
                "Document exceeds maximum size."
            )