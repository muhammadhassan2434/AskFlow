from app.core.config import settings


print("=" * 50)
print("Application Loaded")
print(f"Name      : {settings.APP_NAME}")
print(f"Environment: {settings.APP_ENV}")
print(f"Host      : {settings.HOST}")
print(f"Port      : {settings.PORT}")
print("=" * 50)