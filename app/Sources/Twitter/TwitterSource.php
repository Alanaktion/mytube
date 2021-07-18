<?php

namespace App\Sources\Twitter;

use App\Sources\Source;
use App\Sources\SourceChannel;
use App\Sources\SourcePlaylist;
use App\Sources\SourceVideo;
use App\Sources\Twitter\Source\TwitterChannel;
use App\Sources\Twitter\Source\TwitterVideo;
use Illuminate\View\View;

class TwitterSource implements Source
{
    public function getSourceType(): string
    {
        return 'twitter';
    }

    public function getDisplayName(): string
    {
        return 'Twitter';
    }

    public function getIcon(): View
    {
        return view('sources.icon-twitter');
    }

    public function video(): SourceVideo
    {
        return new TwitterVideo();
    }

    public function channel(): SourceChannel
    {
        return new TwitterChannel();
    }

    public function playlist(): ?SourcePlaylist
    {
        return null;
    }
}
