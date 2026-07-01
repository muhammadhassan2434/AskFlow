from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    """
    Application Settings

    All configuration is loaded from the .env file.
    """

    APP_NAME: str = "AskFlow AI"
    APP_ENV: str = "local"

    HOST: str = "127.0.0.1"
    PORT: int = 8001

    API_KEY: str

    LARAVEL_URL: str
    LARAVEL_API_KEY: str

    model_config = SettingsConfigDict(
        env_file=".env",
        env_file_encoding="utf-8",
        case_sensitive=True,
        extra="ignore",
    )


settings = Settings()