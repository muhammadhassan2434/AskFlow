from app.models.bot import Bot
from app.models.bot_source import BotSource
from app.models.text_source import TextSource


class TextSourceMapper:
    """
    Converts BotSource into TextSource.
    """

    def map(
        self,
        bot: Bot,
        source: BotSource,
    ) -> TextSource:

        return TextSource(
            id=source.id,
            bot_id=bot.id,
            workspace_id=bot.workspace_id,
            title=source.title,
            content=source.content or "",
            status=source.status,
        )