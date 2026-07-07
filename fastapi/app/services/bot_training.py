from app.clients.laravel_client import LaravelClient
from app.schemas.bot import TrainBotRequest
from app.models.bot import Bot
from app.loaders.loader_factory import LoaderFactory


class BotTrainingService:

    def __init__(self):
        self.laravel = LaravelClient()

    def train(self, payload: TrainBotRequest) -> None:

        bot = self.fetch_bot(payload.bot_id)

        for source in bot.sources:

            loader = LoaderFactory.make(source)

            print(
                f"Using {loader.__class__.__name__}"
            )

            loader.load(bot,source)

    def fetch_bot(self, bot_id: int) -> Bot:
        """
        Fetch bot details from Laravel.
        """

        return self.laravel.get_bot(bot_id)

        # return response["data"]
        # return BotMapper.map(response["data"])