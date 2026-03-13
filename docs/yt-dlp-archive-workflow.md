# yt-dlp archive workflow

This guide covers downloading from original source platforms with `yt-dlp` in a way that is:

- web-compatible for playback in MyTube,
- structured for filesystem imports, and
- enriched with sidecar metadata/thumbnails that the importer can use.

## Goals

A good download layout should preserve:

1. A source ID in the filename (for importer matching)
2. Sidecar metadata (`.info.json`) next to media files
3. Sidecar thumbnails next to media files
4. Browser-friendly container/codecs where possible

## Recommended starting command

```bash
NAME="%(upload_date>%Y-%m-%d)s - %(title)s [%(id)s].%(ext)s"
yt-dlp --output "%(uploader)s/$NAME" --windows-filenames \
    --format-sort vcodec,aext \
    --merge-output-format mp4 \
    --write-info-json --mtime \
    --write-thumbnail \
    "$@"
```

Why this works well for MyTube:

- `[(id)]` in the filename helps source matchers resolve video IDs.
- `--write-info-json` gives the importer sidecar metadata for richer local imports.
- `--write-thumbnail` stores local image sidecars that can be imported as thumbnails.
- `--merge-output-format mp4` improves cross-browser compatibility.

## More web-compatible format selection

To prefer H.264 video + AAC audio (widely supported by browsers), use:

```bash
yt-dlp \
  -f "bv*[vcodec^=avc1]+ba[acodec^=mp4a]/b[ext=mp4]/bv*+ba/b" \
  --merge-output-format mp4 \
  --output "%(uploader)s/%(upload_date>%Y-%m-%d)s - %(title)s [%(id)s].%(ext)s" \
  --windows-filenames \
  --write-info-json --write-thumbnail --mtime \
  "$@"
```

This keeps quality high while reducing cases where browser playback needs transcoding.

## Import-friendly directory examples

Single URL:

```bash
yt-dlp --output "%(uploader)s/%(upload_date>%Y-%m-%d)s - %(title)s [%(id)s].%(ext)s" \
  --windows-filenames --format-sort vcodec,aext --merge-output-format mp4 \
  --write-info-json --write-thumbnail --mtime \
  "https://www.youtube.com/watch?v=dQw4w9WgXcQ"
```

Playlist URL:

```bash
yt-dlp --output "%(uploader)s/%(playlist_title)s/%(playlist_index)03d - %(title)s [%(id)s].%(ext)s" \
  --windows-filenames --format-sort vcodec,aext --merge-output-format mp4 \
  --write-info-json --write-thumbnail --mtime \
  "https://www.youtube.com/playlist?list=PLxxxx"
```

Channel/archive run:

```bash
yt-dlp --download-archive .yt-dlp-archive.txt \
  --output "%(uploader)s/%(upload_date>%Y-%m-%d)s - %(title)s [%(id)s].%(ext)s" \
  --windows-filenames --format-sort vcodec,aext --merge-output-format mp4 \
  --write-info-json --write-thumbnail --mtime \
  "https://www.youtube.com/@example/videos"
```

## Import into MyTube

After downloading:

```bash
php artisan import:filesystem /path/to/archive
```

Helpful options:

```bash
php artisan import:filesystem --source=youtube /path/to/archive
php artisan import:filesystem --retry /path/to/archive
```

## Troubleshooting

- Files imported without rich metadata: ensure `.info.json` files are present beside video files.
- Thumbnails missing: confirm `--write-thumbnail` was enabled and image sidecars exist.
- ID match failures: ensure filenames include `[%(id)s]` close to the file extension.
- Playback incompatibility: prefer MP4 container and AVC/AAC-compatible source formats.
