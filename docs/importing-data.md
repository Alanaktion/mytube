# Importing data

MyTube supports importing metadata from multiple source adapters and input types.

## Supported source adapters

List available adapters in your current deployment:

```bash
php artisan import:sources
```

Typical adapters include YouTube, Twitch, Twitter, and Floatplane.

## Import videos by URL

Import one or many video URLs:

```bash
php artisan import:url https://www.youtube.com/watch?v=PF4YxGsF1DY
php artisan import:url https://youtu.be/PF4YxGsF1DY https://www.twitch.tv/videos/123456789
```

Force a specific source adapter when auto-detection is ambiguous:

```bash
php artisan import:url --source=youtube https://youtu.be/PF4YxGsF1DY
```

## Import playlists

```bash
php artisan import:playlist-url https://www.youtube.com/playlist?list=PLxxxx
```

Import playlist metadata without importing all items immediately:

```bash
php artisan import:playlist-url --no-items https://www.youtube.com/playlist?list=PLxxxx
```

## Import channels

```bash
php artisan import:channel-url https://www.youtube.com/@example
```

Optionally include related videos/playlists while importing:

```bash
php artisan import:channel-url --playlists --videos https://www.youtube.com/@example
```

## Import local filesystem media

Scan a directory recursively and match source IDs from filenames:

```bash
php artisan import:filesystem /media/archive
```

Use a specific source and retry previously failed entries:

```bash
php artisan import:filesystem --source=youtube --retry /media/archive
```

Exclude paths while scanning:

```bash
php artisan import:filesystem --exclude='*clips*' --exclude='*tmp*' /media/archive
```

## Sidecar metadata and thumbnails

When present, MyTube can use local sidecar files (for example `*.info.json`) and local images to improve imported metadata quality and thumbnail coverage.

Recommended `yt-dlp` archive naming keeps source IDs near file extensions to improve matching reliability.

## Import troubleshooting

- If a URL fails to match, try `--source=<type>`.
- If filesystem imports skip files, verify IDs in filenames.
- If imports fail repeatedly, re-run with `--retry` and inspect logs in `storage/logs`.
