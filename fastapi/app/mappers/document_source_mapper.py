from app.models.bot import Bot
from app.models.bot_source import BotSource
from app.models.document_source import DocumentSource


class DocumentSourceMapper:

    @staticmethod
    def map(
        bot: Bot,
        source: BotSource,
    ) -> DocumentSource:

        return DocumentSource(
            id=source.id,
            bot_id=bot.id,
            workspace_id=bot.workspace_id,
            title=source.title,
            file_name=source.file_name,
            file_path=source.file_path,
            file_url=source.file_url,
            file_type=source.file_type,
            file_size=source.file_size,
            status=source.status,
        )