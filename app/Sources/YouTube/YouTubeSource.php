<?php

namespace App\Sources\YouTube;

use App\Sources\Source;
use App\Sources\SourceChannel;
use App\Sources\SourcePlaylist;
use App\Sources\SourceVideo;
use App\Sources\YouTube\Source\YouTubeChannel;
use App\Sources\YouTube\Source\YouTubePlaylist;
use App\Sources\YouTube\Source\YouTubeVideo;
use Illuminate\View\View;

class YouTubeSource implements Source
{
    public function getSourceType(): string
    {
        return 'youtube';
    }

    public function getDisplayName(): string
    {
        return 'YouTube';
    }

    public function getIcon(): View
    {
        return view('sources.icon-youtube');
    }

    public function video(): SourceVideo
    {
        return new YouTubeVideo();
    }

    public function channel(): SourceChannel
    {
        return new YouTubeChannel();
    }

    public function playlist(): ?SourcePlaylist
    {
        return new YouTubePlaylist();
    }
}
