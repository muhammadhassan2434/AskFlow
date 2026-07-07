from abc import ABC, abstractmethod

from app.models.download_result import DownloadResult
from app.models.parser_result import ParserResult


class BaseParser(ABC):
    """
    Base contract for every document parser.
    """

    @abstractmethod
    def parse(
        self,
        download: DownloadResult,
    ) -> ParserResult:
        raise NotImplementedError