from langchain_text_splitters import RecursiveCharacterTextSplitter

from app.chunking.base_chunker import BaseChunker
from app.core.config import settings
from app.models.document_chunk import DocumentChunk
from app.models.extracted_source import ExtractedSource


class RecursiveChunker(BaseChunker):
    """
    Splits text into overlapping chunks while
    preserving as much semantic context as possible.
    """

    def __init__(self) -> None:

        self.splitter = RecursiveCharacterTextSplitter(
            chunk_size=settings.CHUNK_SIZE,
            chunk_overlap=settings.CHUNK_OVERLAP,
            separators=[
                "\n\n",
                "\n",
                ". ",
                " ",
                "",
            ],
        )

    def chunk(
        self,
        source: ExtractedSource,
    ) -> list[DocumentChunk]:

        chunks = self.splitter.split_text(source.text)

        return [
            DocumentChunk(
                bot_id=source.bot_id,
                workspace_id=source.workspace_id,
                source_id=source.source_id,
                source_type=source.source_type,
                title=source.title,
                chunk_index=index,
                text=text,
                metadata={
                    **source.metadata,
                    "chunk_index": index,
                    "chunk_count": len(chunks),
                },
            )
            for index, text in enumerate(chunks)
        ]