<?php

namespace App\Sources\Floatplane;

use App\Sources\Source;
use App\Sources\SourceChannel;
use App\Sources\SourcePlaylist;
use App\Sources\SourceVideo;
use App\Sources\Floatplane\Source\FloatplaneChannel;
use App\Sources\Floatplane\Source\FloatplaneVideo;
use Illuminate\View\View;

class FloatplaneSource implements Source
{
    public function getSourceType(): string
    {
        return 'floatplane';
    }

    public function getDisplayName(): string
    {
        return 'Floatplane';
    }

    public function getIcon(): View
    {
        return view('sources.icon-floatplane');
    }

    public function video(): SourceVideo
    {
        return new FloatplaneVideo();
    }

    public function channel(): SourceChannel
    {
        return new FloatplaneChannel();
    }

    public function playlist(): ?SourcePlaylist
    {
        return null;
    }
}
