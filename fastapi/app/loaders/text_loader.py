from app.loaders.base_loader import BaseLoader
from app.models.bot_source import BotSource
from app.models.extracted_source import ExtractedSource
from app.models.bot import Bot

class TextLoader(BaseLoader):

    def load(self,
             bot: Bot,
              source: BotSource) -> ExtractedSource:
        raise NotImplementedError("Text loader not implemented.")