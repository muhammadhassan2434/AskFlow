from abc import ABC, abstractmethod

from app.models.parser_result import ParserResult
from app.models.extracted_source import ExtractedSource


class BaseNormalizer(ABC):
    """
    Base contract for every text normalizer.
    """

    @abstractmethod
    def normalize(
        self,
        parser_result: ParserResult,
    ) -> ExtractedSource:
        raise NotImplementedError