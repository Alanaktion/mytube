# MyTube

This is a web app in early stages, with the end goal of providing a self-hosted YouTube mirror. It is currently capable of storing metadata for existing local video files, and providing a basic web interface for discovering and watching the local videos.

## Requirements

- PHP 7.3 or later, PHP 8 recommended
- MySQL 8 (or any RDBMS supported by Laravel)
- A web server with support for rewrites and following symbolic links (nginx recommended)
- Latest Node.js LTS (for compiling static assets)

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
vim .env # Add database and YouTube API info
```

Compile the assets and set up the database:

```bash
npm run production
php artisan migrate
```

From here, you can set up your web server to point to the `public/` directory and the site should be live.

### Queues

Many features require the ability to queue actions such as large imports and downloads, to run them asynchronously from the web UI.

This can work with just a database, but it works best if you have something like Redis installed. There are really only two required steps for a basic queue setup:

1. Set the `QUEUE_CONNECTION` value in your `.env` file to whatever you want to use for queues. You can use `database` by default, or `redis` if you have that installed.
2. Start a command-line process to run the queued jobs: `php artisan queue:work --queue=default,download`.

If you choose to use Redis, you may require additional changes to the Redis configuration in your `.env` file, but the defaults should work for a typical local installation.

A production environment will likely work best with additional setup to ensure the queue workers stay running. See the [Laravel Queue Worker documentation](https://laravel.com/docs/queues#running-the-queue-worker) for more details. In particular, heavily-used environments may find value in running a separate worker for each queue, or running multiple download queues in parallel if the additional IO is not an issue.

## Administration

The web interface includes an administrative UI that offers basic functions for importing data. It's accessible at the `/admin` path on the app.

You can create an admin user from the command-line:

```bash
php artisan user:add
```

## Importing data

### Local video files

You can import existing video files from your local filesystem:

```bash
php artisan youtube:import-fs <directory>
```

This will scan the given directory for files that include YouTube video IDs in their filenames, and will fetch and store metadata from those videos. The videos will only be recognized if they have the YouTube UUID right before the file extension, as is the default in `youtube-dl`.

For best browser support, files should be in MP4 containers, with MPEG4 video and AAC audio. To download videos in this compatible single-file format:

```bash
youtube-dl -f bestvideo+bestaudio[ext=m4a]/bestvideo+bestaudio/best \
    --merge-output-format mp4 \
    <URL>
```

This will avoid video transcoding where possible, but may occasionally require re-encoding the audio stream (which requires `ffmepg` or `avconv` to be available). Using only the `merge-output-format` option will usually work, but may not download the ideal source formats, requiring more transcoding and potentially a lower quality result.

### Playlists

Playlists can be imported by their IDs, either from command-line or from the admin web UI.

For CLI:

```bash
php artisan youtube:import-playlist <playlist IDs>
```

### Downloading videos

Any videos that have metadata imported, but no local files (_e.g._ from the playlist import), can have videos downloaded automatically. This requires [youtube-dl](https://youtube-dl.org) to be installed. You should also have `ffmpeg` installed for the best format compatibility.

Starting the download of any videos missing local files can be started from the command-line:

```bash
php artisan youtube:download-videos
```

To configure where the videos are downloaded to, add a new line to `.env`:

```ini
YTDL_DOWNLOAD_DIR=/var/media/videos
```

You can also specify the path to your youtube-dl app if it is not included on your $PATH:

```ini
YTDL_PATH=/usr/local/bin/youtube-dl
```

Keep in mind that this involves downloading the video files from YouTube, and potentially re-encoding some incompatible audio streams. This can require significant network bandwidth, disk IO, and CPU in some cases. You should also be familiar with the YouTube terms of use, including usage of the API and website, to ensure your usage is not contradictory to those terms. The [youtube-dl](https://youtube-dl.org) project may have additional information on these topics.

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
      video {
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
