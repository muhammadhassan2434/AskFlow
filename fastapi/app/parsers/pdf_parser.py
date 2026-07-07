import fitz

from app.exceptions.document import (
    CorruptedDocumentError,
    EmptyParsedDocumentError,
    EncryptedDocumentError,
)
from app.models.download_result import DownloadResult
from app.models.parser_result import ParserResult
from app.parsers.base_parser import BaseParser


class PDFParser(BaseParser):
    """
    Parses PDF documents using PyMuPDF.
    """

    def parse(
        self,
        download: DownloadResult,
    ) -> ParserResult:

        try:

            document = fitz.open(download.local_path)

        except Exception as exc:

            raise CorruptedDocumentError(
                "Unable to open PDF document."
            ) from exc

        try:

            if document.needs_pass:

                raise EncryptedDocumentError(
                    "PDF is password protected."
                )

            pages = []

            for page in document:

                text = page.get_text("text")

                if text:
                    pages.append(text)

            full_text = "\n\n".join(pages).strip()

            if not full_text:

                raise EmptyParsedDocumentError(
                    "No readable text found in PDF."
                )

            return ParserResult(
                text=full_text,
                page_count=document.page_count,
                metadata={
                    "title": document.metadata.get("title"),
                    "author": document.metadata.get("author"),
                    "producer": document.metadata.get("producer"),
                },
            )

        finally:

            document.close()