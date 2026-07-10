import httpx

from app.models.website_download import WebsiteDownload
from app.models.website_source import WebsiteSource


class WebsiteDownloader:

    def download(
        self,
        source: WebsiteSource,
    ) -> WebsiteDownload:

        response = httpx.get(
            source.url,
            timeout=30,
            follow_redirects=True,
            headers={
                "User-Agent": (
                    "AskFlowBot/1.0 "
                    "(https://askflow.ai)"
                )
            },
        )

        response.raise_for_status()

        return WebsiteDownload(
            source=source,
            html=response.text,
            content_type=response.headers.get("Content-Type"),
            status_code=response.status_code,
        )