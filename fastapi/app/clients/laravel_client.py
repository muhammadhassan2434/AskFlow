import httpx

from app.core.config import settings
from app.models.bot import Bot



class LaravelClient:
    def __init__(self):
        self.base_url = settings.LARAVEL_URL.rstrip("/")
        self.headers = {
            "X-API-Key": settings.LARAVEL_API_KEY,
            "Accept": "application/json",
        }

    def get_bot(self, bot_id: int) -> Bot:

        response = httpx.get(
            f"{self.base_url}/api/ai/bots/{bot_id}",
            headers=self.headers,
            timeout=60,
        )

        response.raise_for_status()

        data = response.json()

        return Bot.model_validate(data["data"])