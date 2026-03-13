# Installation

This guide covers a standard self-hosted MyTube setup.

## Requirements

- PHP 8.2+ with Composer
- MySQL 8+ (or another Laravel-supported database)
- Node.js 20+ for front-end assets
- A web server capable of rewrites and symlinks (`nginx` recommended)
- `ffmpeg` for media metadata and conversion workflows

## 1) Install dependencies

```bash
composer install --no-dev
npm ci
```

For development work, omit `--no-dev`.

## 2) Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Then edit `.env` with your database credentials:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mytube
DB_USERNAME=mytube
DB_PASSWORD=change-me
```

## 3) Build assets and migrate

```bash
npm run build
php artisan migrate
```

## 4) Serve the app

Point your web server document root to `public/`.

For local-only testing, you can also use:

```bash
php artisan serve
```

## Optional: first admin user

```bash
php artisan user:add
```

After creating an account, the admin UI is available at `/admin`.

## Common post-install checks

```bash
php artisan import:sources
php artisan route:list
php artisan about
```

If these commands succeed, your base application setup is working.
