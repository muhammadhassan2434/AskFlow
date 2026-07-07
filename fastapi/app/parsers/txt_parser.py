from app.exceptions.document import (
    EmptyParsedDocumentError,
    UnsupportedEncodingError,
)
from app.models.download_result import DownloadResult
from app.models.parser_result import ParserResult
from app.parsers.base_parser import BaseParser


class TXTParser(BaseParser):
    """
    Parses plain text documents.
    """

    ENCODINGS = (
        "utf-8",
        "utf-8-sig",
        "utf-16",
        "latin-1",
    )

    def parse(
        self,
        download: DownloadResult,
    ) -> ParserResult:

        for encoding in self.ENCODINGS:

            try:

                text = download.local_path.read_text(
                    encoding=encoding
                )

                text = text.strip()

                if not text:

                    raise EmptyParsedDocumentError(
                        "Text document is empty."
                    )

                return ParserResult(
                    text=text,
                    metadata={
                        "encoding": encoding,
                    },
                )

            except UnicodeDecodeError:

                continue

        raise UnsupportedEncodingError(
            "Unable to decode text document."
        )