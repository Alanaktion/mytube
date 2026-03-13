# Operations

This page covers background workers, search indexing, and production optimization.

## Queue workers

Many imports and download jobs are designed to run asynchronously.

1. Set a queue backend in `.env` (`QUEUE_CONNECTION=database` or `redis`).
2. Start a worker process:

```bash
php artisan queue:work --queue=default,download
```

For heavier workloads, run separate workers per queue:

```bash
php artisan queue:work --queue=default
php artisan queue:work --queue=download
```

After each deployment, restart workers:

```bash
php artisan queue:restart
```

## Search indexing (Meilisearch)

If `SCOUT_DRIVER=meilisearch` is enabled, initialize indexes:

```bash
php artisan search:index
```

To rebuild from scratch:

```bash
php artisan search:index --flush
```

Use `--flush` carefully in production because it clears indexed data before reimporting.

## Production optimization

Set production app mode:

```ini
APP_ENV=production
APP_DEBUG=false
```

Then cache framework metadata:

```bash
php artisan optimize
```

After changing config/routes/translations, re-run `php artisan optimize`.

## Routine maintenance commands

```bash
# Refresh thumbnails from source metadata / files
php artisan import:thumbnails

# Download missing YouTube thumbnails
php artisan youtube:download-thumbnails --generate

# Generate local thumbnail files
php artisan generate:thumbs

# Remove file links that no longer exist on disk
php artisan import:delete-missing
```

## Operational checklist

- Queue workers are running and supervised
- Disk space is sufficient for media growth
- Search indexes are healthy (if using Meilisearch)
- App caches are rebuilt after deployment
