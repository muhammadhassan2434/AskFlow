class LoaderError(Exception):
    """Base exception for loader-related errors."""


class UnsupportedSourceTypeError(LoaderError):
    """Raised when no loader exists for a source type."""