from app.loaders.base_loader import BaseLoader

from app.mappers.website_source_mapper import WebsiteSourceMapper

from app.website.downloader import WebsiteDownloader
from app.website.validator import WebsiteValidator

from app.parsers.html_parser import HTMLParser

from app.normalizers.text_normalizer import TextNormalizer

from app.models.bot import Bot
from app.models.bot_source import BotSource
from app.models.extracted_source import ExtractedSource


class WebsiteLoader(BaseLoader):
    """
    Handles the complete website loading pipeline.

    Responsibilities
    ----------------
    - Map BotSource -> WebsiteSource
    - Download HTML
    - Validate HTML
    - Parse visible text
    - Normalize text
    - Return ExtractedSource
    """

    def __init__(
        self,
        mapper: WebsiteSourceMapper | None = None,
        downloader: WebsiteDownloader | None = None,
        validator: WebsiteValidator | None = None,
        parser: HTMLParser | None = None,
        normalizer: TextNormalizer | None = None,
    ):

        self.mapper = mapper or WebsiteSourceMapper()

        self.downloader = downloader or WebsiteDownloader()

        self.validator = validator or WebsiteValidator()

        self.parser = parser or HTMLParser()

        self.normalizer = normalizer or TextNormalizer()

    def load(
        self,
        bot: Bot,
        source: BotSource,
    ) -> ExtractedSource:

        website = self.mapper.map(bot, source)

        download = self.downloader.download(website)

        self.validator.validate(download)

        parser_result = self.parser.parse(download)

        normalized = self.normalizer.normalize(parser_result)

        return ExtractedSource(
            source_id=website.id,
            bot_id=website.bot_id,
            workspace_id=website.workspace_id,
            source_type="website",
            title=website.title,
            text=normalized.text,          # ✅ Correct
            metadata=normalized.metadata,  # ✅ Correct
        )