import re

from app.models.normalized_text import NormalizedText
from app.models.parser_result import ParserResult
from app.normalizers.base_normalizer import BaseNormalizer


class TextNormalizer(BaseNormalizer):
    """
    Cleans parser output before chunking.

    Responsibilities:
    - normalize line endings
    - remove control characters
    - collapse whitespace
    - remove excessive blank lines
    - compute statistics
    """

    def normalize(
        self,
        parser_result: ParserResult,
    ) -> NormalizedText:

        text = parser_result.text

        # Windows -> Unix line endings
        text = text.replace("\r\n", "\n")
        text = text.replace("\r", "\n")

        # Remove non-printable characters
        text = re.sub(
            r"[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]",
            "",
            text,
        )

        # Remove trailing spaces
        text = re.sub(
            r"[ \t]+\n",
            "\n",
            text,
        )

        # Collapse multiple spaces
        text = re.sub(
            r"[ \t]{2,}",
            " ",
            text,
        )

        # Maximum two blank lines
        text = re.sub(
            r"\n{3,}",
            "\n\n",
            text,
        )

        text = text.strip()

        return NormalizedText(
            text=text,
            metadata=parser_result.metadata,
        )