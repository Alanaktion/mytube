# MyTube

A self-hostable video archive web app

[![CI](https://github.com/Alanaktion/mytube/actions/workflows/ci.yml/badge.svg)](https://github.com/Alanaktion/mytube/actions/workflows/ci.yml)

## Quick summary

MyTube is a self-hostable archive for online video metadata and local media files. It supports importing from multiple services, managing imports from an admin UI, and accessing content from a website and GraphQL API.

Key capabilities:

- Multi-source imports (YouTube, Twitch, Twitter, Floatplane)
- URL-, playlist-, channel-, and filesystem-based import workflows
- Local media download support with yt-dlp
- Admin tools at `/admin` for imports, queue visibility, and missing media checks
- Public + authenticated GraphQL schemas
- Search with SQL by default, optional Meilisearch indexing
- Light/dark mode and localization support

## Quick start

```bash
composer install --no-dev
pnpm i
cp .env.example .env
php artisan key:generate
php artisan migrate
pnpm run build
```

Then point your web server to `public/`.

## Documentation

- [Installation](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Operations](docs/operations.md)
- [Administration](docs/administration.md)
- [Importing data](docs/importing-data.md)
- [Downloading videos](docs/downloading-videos.md)
- [yt-dlp archive workflow](docs/yt-dlp-archive-workflow.md)
- [GraphQL API](docs/graphql-api.md)

## Common commands

```bash
# List configured source adapters
php artisan import:sources

# Import videos from one or more URLs
php artisan import:url <url> [<url>...]

# Import a playlist
php artisan import:playlist-url <playlist-url>

# Import local files by scanning a directory
php artisan import:filesystem /path/to/video-library

# Download videos that are missing local files
php artisan download:missing --queue
```
