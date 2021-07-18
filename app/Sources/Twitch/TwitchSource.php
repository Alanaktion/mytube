<?php

namespace App\Sources\Twitch;

use App\Sources\Source;
use App\Sources\SourceChannel;
use App\Sources\SourcePlaylist;
use App\Sources\SourceVideo;
use App\Sources\Twitch\Source\TwitchChannel;
use App\Sources\Twitch\Source\TwitchVideo;
use Illuminate\View\View;

class TwitchSource implements Source
{
    public function getSourceType(): string
    {
        return 'twitch';
    }

    public function getDisplayName(): string
    {
        return 'Twitch';
    }

    public function getIcon(): View
    {
        return view('sources.icon-twitch');
    }

    public function video(): SourceVideo
    {
        return new TwitchVideo();
    }

    public function channel(): SourceChannel
    {
        return new TwitchChannel();
    }

    public function playlist(): ?SourcePlaylist
    {
        return null;
    }
}
