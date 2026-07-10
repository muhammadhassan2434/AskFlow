from app.models.bot import Bot
from app.models.bot_source import BotSource
from app.models.website_source import WebsiteSource


class WebsiteSourceMapper:

    def map(
        self,
        bot: Bot,
        source: BotSource,
    ) -> WebsiteSource:

        return WebsiteSource(
            id=source.id,
            bot_id=bot.id,
            workspace_id=bot.workspace_id,
            title=source.title,
            url=source.url,
            status=source.status,
        )