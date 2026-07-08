from abc import ABC, abstractmethod

from app.models.document_chunk import DocumentChunk
from app.models.extracted_source import ExtractedSource


class BaseChunker(ABC):
    """
    Contract for every chunking strategy.
    """

    @abstractmethod
    def chunk(
        self,
        source: ExtractedSource,
    ) -> list[DocumentChunk]:
        raise NotImplementedError