<?php

namespace App\Sources\Test;

use App\Sources\Source;
use App\Sources\SourceChannel;
use App\Sources\SourcePlaylist;
use App\Sources\SourceVideo;
use App\Sources\Test\Source\TestChannel;
use App\Sources\Test\Source\TestVideo;
use Illuminate\View\View;

class TestSource implements Source
{
    public function getSourceType(): string
    {
        return 'test';
    }

    public function getDisplayName(): string
    {
        return 'Test';
    }

    public function getIcon(): View
    {
        return view('sources.icon-youtube');
    }

    public function video(): SourceVideo
    {
        return new TestVideo();
    }

    public function channel(): SourceChannel
    {
        return new TestChannel();
    }

    public function playlist(): ?SourcePlaylist
    {
        return null;
    }
}
