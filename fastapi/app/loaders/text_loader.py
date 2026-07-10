from app.loaders.base_loader import BaseLoader
from app.mappers.text_source_mapper import TextSourceMapper
from app.models.bot import Bot
from app.models.bot_source import BotSource
from app.models.extracted_source import ExtractedSource
from app.models.parser_result import ParserResult
from app.normalizers.text_normalizer import TextNormalizer


class TextLoader(BaseLoader):
    """
    Handles manual text sources.

    Pipeline:

    BotSource
        ↓
    TextSourceMapper
        ↓
    TextNormalizer
        ↓
    ExtractedSource

    """

    def __init__(self):

        self.mapper = TextSourceMapper()

        self.normalizer = TextNormalizer()

    def load(
        self,
        bot: Bot,
        source: BotSource,
    ):

        text_source = self.mapper.map(bot, source)

        parser_result = ParserResult(
            text=text_source.content,
        )

        normalized = self.normalizer.normalize(
          parser_result
        )

        return ExtractedSource(
            source_id=text_source.id,
            bot_id=text_source.bot_id,
            workspace_id=text_source.workspace_id,
            source_type="text",
            title=text_source.title,
            text=normalized.text,
            metadata=normalized.metadata,
        )