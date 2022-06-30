# MyTube

A self-hostable video archive web app

[![CI](https://github.com/Alanaktion/mytube/actions/workflows/ci.yml/badge.svg)](https://github.com/Alanaktion/mytube/actions/workflows/ci.yml)

## Features

- Import video, channel, and playlist metadata from several sources:
    - YouTube
    - Twitch
    - Twitter
    - Floatplane
- Import metadata from web URLs, local filesystem, and video IDs
- Browse imported data from an intuitive web UI
- Download videos automatically with yt-dlp
- Light/dark mode toggle
- Multiple languages
- Administrator interface for importing content
- GraphQL API for accessing public content
- Experimental mobile app built with Expo

## Demo

https://mytube-app-demo.herokuapp.com/

## Requirements

- [PHP](https://php.net) 8 or later with [Composer](https://getcomposer.org)
- [MySQL](https://dev.mysql.com/downloads/) 8 (or any RDBMS supported by Laravel)
- Web server with support for rewrites and following symbolic links ([nginx](https://nginx.org/en/) recommended)
- [NodeJS](https://nodejs.org/en/) LTS 16 or later for building front-end assets

## Setup

The basic setup involves installing Composer and npm dependencies, configuring the app with database and API credentials, and compiling the static web assets.

Install dependencies:

```bash
composer install --no-dev
npm ci
```

Configure the app:

```bash
cp .env.example .env
php artisan key:generate
vim .env # Add database credentials
```

Compile the assets and set up the database:

```bash
npm run build
php artisan migrate
```

From here, you can set up your web server to point to the `public/` directory and the site should be live. For more information on the web server setup, see the [Laravel deployment documentation](https://laravel.com/docs/8.x/deployment).

### Queues

Many features require the ability to queue actions such as large imports and downloads, to run them asynchronously from the web UI.

This can work with just a database, but it works best if you have something like Redis installed. There are really only two required steps for a basic queue setup:

1. Set the `QUEUE_CONNECTION` value in your `.env` file to whatever you want to use for queues. You can use `database` by default, or `redis` if you have that installed.
2. Start a command-line process to run the queued jobs: `php artisan queue:work --queue=default,download`.

If you choose to use Redis, you may require additional changes to the Redis configuration in your `.env` file, but the defaults should work for a typical local installation.

A production environment will likely work best with additional setup to ensure the queue workers stay running. See the [Laravel Queue Worker documentation](https://laravel.com/docs/queues#running-the-queue-worker) for more details. In particular, heavily-used environments may find value in running a separate worker for each queue, or running multiple download queues in parallel if the additional IO is not an issue.

Make sure to restart your queue workers any time you update the app.

### Search indexing

By default, MyTube uses SQL "like" queries to match content when searching. This works fine particularly for smaller databases with diverse content, but is limited in performance and flexibility.

Alteratively, MyTube can use MeiliSearch with Laravel Scout to index content and provide fast, robust searches. It is optional, but makes the search _much_ better and it's highly recommended that you use it.

1. Install and start [MeiliSearch](https://docs.meilisearch.com/learn/getting_started/installation.html)
2. In your `.env` file, set `SCOUT_DRIVER=meilisearch`
3. If you are running multiple instances of MyTube, set a unique index prefix with the `SCOUT_PREFIX` value in your `.env` file
4. Initialize your search indexes: `php artisan search:index`
    - Subsequent changes to content will be updated automatically, but you can use this command to force a re-index with `--force`.

### Production optimization

If you're not running the application for development/contribution purposes, it's recommended to run in optimized production mode for the best performance and to avoid leaking sensitive information like API keys.

1. In `.env`, set `APP_ENV=production` and `APP_DEBUG=false`
2. Run `php artisan optimize` to pre-cache the configuration, routing, etc.

You will need to re-run `php artisan optimize` any time you update the app.

## Administration

The web interface includes an administrative UI that offers basic functions for importing data. It's accessible at the `/admin` path on the app.

You can create an admin user from the command-line:

```bash
php artisan user:add
```

## Importing data

Importing data may require a usable API key or user account for some sources and some object types. In particular, most YouTube, Twitch, and Twitter imports require a related API Key, and Floatplane imports currently rely on a session ID from a logged-in user with access to the imported data.

### Local video files

You can import existing video files from your local filesystem:

```bash
php artisan import:filesystem <directory>
```

This will recursively scan the given directory for files that include YouTube video IDs in their filenames, and will fetch and store metadata from those videos. The videos will only be recognized if they have the YouTube UUID right before the file extension, as is the default in `yt-dlp`.

For best browser support, files should be in MP4 containers, with H.264 video and AAC audio. To download videos in this compatible single-file format:

```bash
yt-dlp -f bv+ba[ext=m4a]/b[ext=mp4]/bv+ba/b \
    --merge-output-format mp4 \
    <URL>
```

This will avoid video transcoding where possible, but may occasionally require re-encoding the audio stream (which requires `ffmpeg` or `avconv` to be available). Using only the `merge-output-format` option will usually work, but may not download the ideal source formats, requiring more transcoding and potentially a lower quality result.

### Playlists

Playlists can be imported by their URLs, either from command-line or from the admin web UI.

For CLI:

```bash
php artisan import:playlist-url <playlist URL/IDs>
```

### Downloading videos

Any YouTube videos that have metadata imported, but no local files (_e.g._ from the playlist import), can have videos downloaded automatically. This requires [yt-dlp](https://github.com/yt-dlp/yt-dlp) to be installed. You should also have `ffmpeg` installed for the best format compatibility.

Starting the download of any videos missing local files can be started from the command-line:

```bash
php artisan youtube:download-videos
```

To configure where the videos are downloaded to, add a new line to `.env`:

```ini
YTDL_DOWNLOAD_DIR=/var/media/videos
```

You can also specify the path to your yt-dlp executable if it is not included on your $PATH:

```ini
YTDL_PATH=/usr/local/bin/yt-dlp
```

Keep in mind that this involves downloading the video files from the source, and potentially re-encoding some incompatible audio streams. This can require significant network bandwidth, disk IO, and CPU in some cases. You should also be familiar with the service's terms of use, including usage of the API and website, to ensure your usage is not contradictory to those terms. The [yt-dlp](https://github.com/yt-dlp/yt-dlp) project may have additional information on these topics.

## GraphQL API

The application provides a GraphQL API for accessing data in the archive. An example implementation of an API client is the [Expo-based mobile app](https://github.com/Alanaktion/mytube-exbo). Most access is not limited to authenticated users, but user-specific functionality will require a valid OAuth bearer token.

This includes a [GraphiQL](https://github.com/graphql/graphiql) web interface for querying the API, available at the `/graphiql` path. This can be disabled by setting the `ENABLE_GRAPHIQL` environment variable to false.

### Examples

This example gets recent videos with some basic metadata, including the channel. The `page` argument is optional.

```graphql
{
  videos(page: 1) {
    data {
      id
      uuid
      title
      published_at
      channel {
        id
        uuid
        title
      }
    }
    current_page
    last_page
    total
  }
}

```

This example searches channels by title.

```graphql
{
  channels(search: "Example") {
    data {
      id
      uuid
      title
    }
    current_page
    last_page
    total
  }
}
```

This example gets a specific video.

```graphql
{
  videos(uuid: "PF4YxGsF1DY") {
    data {
      title
      published_at
    }
  }
}
```

This example gets a list of videos from a specific channel.

```graphql
{
  videos(channel_id: 6) {
    data {
      title
      published_at
    }
    current_page
    last_page
    total
  }
}
```

Many of these options can be combined. For example, you can search videos by title, and filter the result to a specific channel. Every result set is a paginated list, even if only a specific resource is selected (_e.g._ when the `id` or `uuid` argument is used).
