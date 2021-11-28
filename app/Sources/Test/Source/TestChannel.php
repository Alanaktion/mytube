<?php

namespace App\Sources\Test\Source;

use App\Models\Channel;
use App\Sources\SourceChannel;

class TestChannel implements SourceChannel
{
    public function getField(): string
    {
        return 'uuid';
    }

    public function import(string $id): Channel
    {
        return Channel::create([
            'uuid' => $id,
            // TODO: Add more fields
        ]);
    }

    public function matchUrl(string $url): ?string
    {
        return null;
    }

    public function getSourceUrl(Channel $channel): string
    {
        return 'https://www.example.com/c/' . $channel->uuid;
    }
}
