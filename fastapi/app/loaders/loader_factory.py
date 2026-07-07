from app.exceptions.loader import UnsupportedSourceTypeError
from app.loaders.base_loader import BaseLoader
from app.loaders.document_loader import DocumentLoader
from app.loaders.text_loader import TextLoader
from app.loaders.website_loader import WebsiteLoader
from app.models.bot_source import BotSource


class LoaderFactory:
    """
    Creates the appropriate loader for a bot source.
    """

    _loaders: dict[str, type[BaseLoader]] = {
        "document": DocumentLoader,
        "website": WebsiteLoader,
        "text": TextLoader,
    }

    @classmethod
    def make(cls, source: BotSource) -> BaseLoader:

        loader_class = cls._loaders.get(source.type)

        if loader_class is None:
            raise UnsupportedSourceTypeError(
                f"Unsupported source type: '{source.type}'."
            )

        return loader_class()