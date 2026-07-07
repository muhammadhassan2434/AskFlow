from abc import ABC, abstractmethod

from app.models.bot import Bot
from app.models.bot_source import BotSource
from app.models.extracted_source import ExtractedSource


class BaseLoader(ABC):
    """
    Base contract for every knowledge loader.
    """

    @abstractmethod
    def load(
        self,
        bot: Bot,
        source: BotSource,
    ) -> ExtractedSource:
        """
        Load a knowledge source and return extracted text.
        """
        raise NotImplementedError