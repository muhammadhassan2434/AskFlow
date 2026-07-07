class DocumentError(Exception):
    """Base exception for document processing."""


class DocumentDownloadError(DocumentError):
    """Raised when a document cannot be downloaded."""


class DocumentNotFoundError(DocumentError):
    """Raised when the remote document does not exist."""


class DocumentValidationError(DocumentError):
    """Base validation exception."""


class DocumentNotReadableError(DocumentValidationError):
    """Document cannot be read."""


class UnsupportedDocumentTypeError(DocumentValidationError):
    """Unsupported document extension."""


class InvalidMimeTypeError(DocumentValidationError):
    """Invalid MIME type."""


class EmptyDocumentError(DocumentValidationError):
    """Document is empty."""


class DocumentTooLargeError(DocumentValidationError):
    """Document exceeds configured size."""

class DocumentParseError(DocumentError):
    """Base exception for parser errors."""


class CorruptedDocumentError(DocumentParseError):
    """The document is corrupted."""


class EncryptedDocumentError(DocumentParseError):
    """The document is password protected."""


class EmptyParsedDocumentError(DocumentParseError):
    """No text could be extracted from the document."""

class UnsupportedEncodingError(DocumentParseError):
    """Unable to decode the text document."""

class CorruptedDocxError(DocumentParseError):
    """Raised when the DOCX document is invalid or corrupted."""