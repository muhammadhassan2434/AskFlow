from app.chunking.recursive_chunker import RecursiveChunker
from app.chunking.base_chunker import BaseChunker


class ChunkFactory:
    """
    Returns the configured chunking strategy.
    """

    @staticmethod
    def make() -> BaseChunker:
        return RecursiveChunker()