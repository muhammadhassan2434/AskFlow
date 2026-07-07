from app.exceptions.document import UnsupportedDocumentTypeError
from app.models.download_result import DownloadResult
from app.parsers.base_parser import BaseParser
from app.parsers.docx_parser import DOCXParser
from app.parsers.pdf_parser import PDFParser
from app.parsers.txt_parser import TXTParser


class ParserFactory:
    """
    Creates the appropriate parser
    for a downloaded document.
    """

    _parsers = {
        "pdf": PDFParser,
        "doc": DOCXParser,
        "docx": DOCXParser,
        "txt": TXTParser,
    }

    @classmethod
    def make(
        cls,
        download: DownloadResult,
    ) -> BaseParser:

        extension = (
            download.source.file_type.lower()
            if download.source.file_type
            else ""
        )

        parser = cls._parsers.get(extension)

        if parser is None:
            raise UnsupportedDocumentTypeError(
                f"Unsupported document type: {extension}"
            )

        return parser()