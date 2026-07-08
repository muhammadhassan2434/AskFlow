from pathlib import Path
import logging

from app.document.downloader import DocumentDownloader
from app.document.validator import DocumentValidator
from app.loaders.base_loader import BaseLoader
from app.mappers.document_source_mapper import DocumentSourceMapper
from app.models.bot import Bot
from app.models.bot_source import BotSource
from app.models.download_result import DownloadResult
from app.models.extracted_source import ExtractedSource
from app.parsers.parser_factory import ParserFactory
from app.normalizers.text_normalizer import TextNormalizer

logger = logging.getLogger(__name__)


class DocumentLoader(BaseLoader):
    """
    Handles the complete document loading pipeline.

    Responsibilities:
    - Map BotSource -> DocumentSource
    - Download document
    - Validate document
    - Parse document (next phase)
    - Normalize text (next phase)
    - Clean up temporary files
    """

    def __init__(
        self,
        mapper: DocumentSourceMapper | None = None,
        downloader: DocumentDownloader | None = None,
        validator: DocumentValidator | None = None,
    ) -> None:

        self.mapper = mapper or DocumentSourceMapper()

        self.downloader = downloader or DocumentDownloader()

        self.validator = validator or DocumentValidator()

        self.parser_factory = ParserFactory()

        self.normalizer = TextNormalizer()

    def load(
        self,
        bot: Bot,
        source: BotSource,
    ) -> ExtractedSource:
        """
        Load a document source.

        Parsing and normalization will be implemented
        in the next phase.
        """

        document = self.mapper.map(bot, source)

        download = self.downloader.download(document)

        try:

            self.validator.validate(download)

            parser = self.parser_factory.make(download)

            parser_result = parser.parse(download)

            normalized = self.normalizer.normalize(parser_result)

            return ExtractedSource(
                source_id=document.id,
                bot_id=document.bot_id,
                workspace_id=document.workspace_id,
                source_type="document",
                title=document.title,
                text=normalized.text,
                metadata=normalized.metadata,
            )

        finally:

            self._cleanup(download)

    def _cleanup(
        self,
        download: DownloadResult,
    ) -> None:
        """
        Delete the temporary downloaded file.
        """

        try:

            if download.local_path.exists():

                logger.debug(
                    "Deleting temporary file: %s",
                    download.local_path,
                )

                download.local_path.unlink()

        except Exception as exc:

            logger.warning(
                "Failed to delete temporary file '%s': %s",
                download.local_path,
                exc,
            )