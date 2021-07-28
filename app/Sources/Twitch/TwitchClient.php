<?php

namespace App\Sources\Twitch;

use Exception;
use romanzipp\Twitch\Enums\GrantType;
use romanzipp\Twitch\Twitch as TwitchLib;

class TwitchClient
{
    /**
     * @var \romanzipp\Twitch\Twitch
     */
    protected $client;

    /**
     * @link https://github.com/romanzipp/Laravel-Twitch#oauth-client-credentials-flow
     */
    public function __construct()
    {
        $this->client = new TwitchLib();

        // Get access token
        $result = $this->client->getOAuthToken(null, GrantType::CLIENT_CREDENTIALS, ['user_read']);
        if (!$result->success()) {
            throw new Exception($result->getErrorMessage());
        }
        $this->client->setToken($result->data()->access_token);
    }

    /**
     * @link https://dev.twitch.tv/docs/api/reference#get-users
     */
    public function getUser(string $id, string $field = 'login'): array
    {
        $result = $this->client->getUsers([
            $field => $id,
        ]);
        if (!$result->success()) {
            throw new Exception($result->getErrorMessage());
        }
        return json_decode($result->getResponse()->getBody(), true)['data'][0];
    }

    /**
     * @link https://dev.twitch.tv/docs/api/reference#get-videos
     */
    public function getVideos(int $id, string $field = 'id'): array
    {
        $result = $this->client->getVideos([
            $field => $id,
        ]);
        if (!$result->success()) {
            throw new Exception($result->getErrorMessage());
        }
        return json_decode($result->getResponse()->getBody(), true)['data'];
    }

    /**
     * @link https://dev.twitch.tv/docs/api/reference#get-clips
     */
    public function getClips(string $id, string $field = 'id'): array
    {
        $result = $this->client->getClips([
            $field => $id,
        ]);
        if (!$result->success()) {
            throw new Exception($result->getErrorMessage());
        }
        return json_decode($result->getResponse()->getBody(), true)['data'];
    }
}
