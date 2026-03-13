# Administration

The admin interface is available at `/admin` and provides import-focused management tools.

## Create an admin-capable user

Use the interactive command:

```bash
php artisan user:add
```

Then sign in through the web UI.

## Admin UI functions

The admin area includes workflows for:

- Importing videos by URL
- Importing playlists by URL
- Importing channels by URL
- Reviewing missing media and queue-related status pages

## CLI + admin workflow example

A common pattern for large imports:

1. Use admin UI to submit channel/playlist URLs.
2. Run queue workers for async processing:

```bash
php artisan queue:work --queue=default,download
```

3. Trigger missing video downloads in batch:

```bash
php artisan download:missing --queue
```

4. Check `/admin/queue` and `/admin/missing` for status and follow-up actions.

## Token management (user context)

MyTube includes token routes, enabling authenticated user-specific actions. If your workflow includes API clients, create and manage tokens from the user-facing account area, then use them as Bearer tokens for authenticated endpoints (including GraphQL `user` schema operations).
