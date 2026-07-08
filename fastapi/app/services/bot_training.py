from app.clients.laravel_client import LaravelClient
from app.schemas.bot import TrainBotRequest
from app.models.bot import Bot
from app.loaders.loader_factory import LoaderFactory
from app.chunking.chunk_factory import ChunkFactory

class BotTrainingService:

    def __init__(self):
        self.laravel = LaravelClient()
        self.chunker = ChunkFactory.make()

    def train(self, payload: TrainBotRequest) -> None:

        bot = self.fetch_bot(payload.bot_id)

        for source in bot.sources:

            loader = LoaderFactory.make(source)

            extracted = loader.load(bot, source)

            chunks = self.chunker.chunk(extracted)

            print("=" * 80)
            print(f"Source: {source.title}")
            print(f"Chunks: {len(chunks)}")
            print("=" * 80)

            for chunk in chunks:
                print(chunk.chunk_index, len(chunk.text))

    def fetch_bot(self, bot_id: int) -> Bot:
        """
        Fetch bot details from Laravel.
        """

        return self.laravel.get_bot(bot_id)

        # return response["data"]
        # return BotMapper.map(response["data"])