# Downloading videos

MyTube can download video files for records that have metadata but no local media.

For source-first archive downloads (with importer-friendly sidecars and naming), see [yt-dlp archive workflow](yt-dlp-archive-workflow.md).

## Prerequisites

- `yt-dlp` installed
- `ffmpeg` installed (recommended for best format compatibility)
- Queue worker available if using queued downloads

## Configure download settings

In `.env`:

```ini
YTDL_DOWNLOAD_DIR=/var/media/videos
YTDL_PATH=/usr/local/bin/yt-dlp
```

`YTDL_PATH` is optional when `yt-dlp` is discoverable on `$PATH`.

## Download missing files

Run inline (synchronous):

```bash
php artisan download:missing
```

Queue downloads to the `download` queue:

```bash
php artisan download:missing --queue
php artisan queue:work --queue=download
```

## Practical `yt-dlp` format guidance

For broad browser compatibility (single-file MP4 with H.264/AAC where possible):

```bash
yt-dlp -f bv+ba[ext=m4a]/b[ext=mp4]/bv+ba/b \
  --merge-output-format mp4 \
  <URL>
```

This often reduces transcoding needs while preserving quality.

## Operational notes

- Download jobs can consume significant network, IO, and CPU.
- Validate storage capacity before large backfills.
- Respect source platform terms of service and API policies.
