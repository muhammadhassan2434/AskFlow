from app.clients.laravel_client import LaravelClient
from app.schemas.bot import TrainBotRequest


class BotTrainingService:

    def __init__(self):
        self.laravel = LaravelClient()

    def train(self, payload: TrainBotRequest) -> None:

        bot = self.fetch_bot(payload.bot_id)

        print(bot)

    def fetch_bot(self, bot_id: int) -> dict:
        """
        Fetch bot details from Laravel.
        """

        response = self.laravel.get_bot(bot_id)

        return response["data"]