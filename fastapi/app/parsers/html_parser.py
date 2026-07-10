from bs4 import BeautifulSoup

from app.models.parser_result import ParserResult
from app.models.website_download import WebsiteDownload
from app.parsers.base_parser import BaseParser


class HTMLParser(BaseParser):

    def parse(
        self,
        download: WebsiteDownload,
    ) -> ParserResult:

        soup = BeautifulSoup(
            download.html,
            "lxml",
        )

        for tag in soup(
            [
                "script",
                "style",
                "noscript",
                "svg",
                "footer",
                "header",
            ]
        ):
            tag.decompose()

        text = soup.get_text(
            separator="\n",
            strip=True,
        )

        return ParserResult(
            text=text,
            metadata={
                "url": download.source.url,
            },
        )