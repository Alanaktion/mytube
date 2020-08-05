# MyTube

This is a web app in early stages, with the end goal of providing a self-hosted YouTube mirror. The initial goal is to archive metadata about YouTube videos found on a filesystem, _e.g._ from youtube-dl.

## Usage

Right now, only a back-end exists, with no usable front-end. Do this stuff:

```bash
composer install
cp .env.example .env
php artisan key:generate
vim .env # Add database and YouTube API info
php artisan migrate
```

You can then import videos from your filesystem:

```bash
php artisan youtube:import-fs <path to videos>
```

This will scan the given directory for files that include YouTube video IDs in their filenames, and will fetch and store metadata from those videos.
