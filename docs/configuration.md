# Configuration

This guide focuses on the settings most commonly adjusted after installation.

## Core app settings

In `.env`, set your application mode and URL:

```ini
APP_NAME=MyTube
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mytube.example.com
```

Use `APP_DEBUG=true` only in local development.

## Database

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mytube
DB_USERNAME=mytube
DB_PASSWORD=change-me
```

## Queue backend

MyTube can run with a database queue, but Redis is often better for larger imports and downloads.

```ini
QUEUE_CONNECTION=database
```

or

```ini
QUEUE_CONNECTION=redis
```

If you use Redis, also configure your `REDIS_*` values.

## yt-dlp integration

Configure where downloaded media is stored and, if needed, where the executable is located:

```ini
YTDL_DOWNLOAD_DIR=/var/media/videos
YTDL_PATH=/usr/local/bin/yt-dlp
```

`YTDL_PATH` is optional if `yt-dlp` is already on your `$PATH`.

## External video file URLs (optional)

If downloaded files are stored on disk paths that should be served from a separate web host, configure a filesystem prefix to URL mapping:

```ini
VIDEO_FILE_FILESYSTEM_PREFIX=/storage/Videos
VIDEO_FILE_URL_PREFIX=https://videos.example.com/~user/videos
```

With this config, a stored file path like `/storage/Videos/example.mp4` is served as `https://videos.example.com/~user/videos/example.mp4`.

For multiple mappings, provide comma-separated values in matching order:

```ini
VIDEO_FILE_FILESYSTEM_PREFIXES=/storage/Videos,/mnt/archive
VIDEO_FILE_URL_PREFIXES=https://videos.example.com/~user/videos,https://archive.example.com/videos
```

The first filesystem prefix maps to the first URL prefix, the second to the second, and so on.

## Search backend (optional)

By default, MyTube uses SQL `LIKE` searching. For better search quality/performance with larger libraries, use Meilisearch:

```ini
SCOUT_DRIVER=meilisearch
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=
SCOUT_PREFIX=mytube_
```

Then run:

```bash
php artisan search:index
```

## Source credentials

Some source imports need API credentials or authenticated sessions. Review `config/services.php` and the source-related env values in your `.env` file before importing at scale.

Useful check:

```bash
php artisan import:sources
```

That command confirms which source adapters are currently available in your installation.
