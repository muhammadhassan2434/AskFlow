import httpx

from app.core.config import settings


class LaravelClient:
    def __init__(self):
        self.base_url = settings.LARAVEL_URL.rstrip("/")
        self.headers = {
            "X-API-Key": settings.LARAVEL_API_KEY,
            "Accept": "application/json",
        }

    def get_bot(self, bot_id: int) -> dict:
        response = httpx.get(
            f"{self.base_url}/api/ai/bots/{bot_id}",
            headers=self.headers,
            timeout=60,
        )

        response.raise_for_status()

        return response.json()