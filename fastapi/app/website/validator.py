from app.models.website_download import WebsiteDownload


class WebsiteValidator:

    def validate(
        self,
        download: WebsiteDownload,
    ) -> None:

        if not download.html.strip():
            raise ValueError("Website is empty.")

        if "text/html" not in (download.content_type or ""):
            raise ValueError("URL is not an HTML page.")