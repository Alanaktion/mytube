<?php

namespace App\Sources\Twitch;

use App\Exceptions\ImportException;
use TwitchApi\HelixGuzzleClient;
use TwitchApi\TwitchApi;

class TwitchClient
{
    protected TwitchApi $client;
    protected string $token;

    public function __construct()
    {
        $clientId = config('twitch-api.client_id');
        $clientSecret = config('twitch-api.client_secret');

        $helixGuzzleClient = new HelixGuzzleClient($clientId);
        $twitchApi = new TwitchApi($helixGuzzleClient, $clientId, $clientSecret);
        $this->client = $twitchApi;
        $oauth = $twitchApi->getOauthApi();

        try {
            $token = $oauth->getAppAccessToken();
            $data = json_decode($token->getBody()->getContents());
            $this->token = $data->access_token;
        } catch (\Exception) {
            //TODO: Handle auth errors
        }
    }

    /**
     * @link https://dev.twitch.tv/docs/api/reference#get-users
     *
     * @return array<string, mixed>
     */
    public function getUser(string $id, string $field = 'login'): array
    {
        $api = $this->client->getUsersApi();
        if ($field == 'login') {
            $response = $api->getUserByUsername($this->token, $id);
        } else {
            $response = $api->getUserById($this->token, $id);
        }
        if ($response->getStatusCode() != 200) {
            // TODO: provide better exception messaging
            $result = json_decode($response->getBody()->getContents());
            throw new ImportException($result);
        }
        return json_decode($response->getBody(), true)['data'][0];
    }

    /**
     * @link https://dev.twitch.tv/docs/api/reference#get-videos
     *
     * @return array<string, mixed>[]
     */
    public function getVideos(int $id, string $field = 'id'): array
    {
        $response = $this->client->getVideosApi()->getVideos($this->token, [$id]);
        if ($response->getStatusCode() != 200) {
            // TODO: provide better exception messaging
            $result = json_decode($response->getBody()->getContents());
            throw new ImportException($result);
        }
        return json_decode($response->getBody(), true)['data'];
    }

    /**
     * @link https://dev.twitch.tv/docs/api/reference#get-clips
     *
     * @return array<string, mixed>[]
     */
    public function getClips(string $id, string $field = 'id'): array
    {
        $response = $this->client->getClipsApi()->getClips($this->token, clipIds: $id);
        if ($response->getStatusCode() != 200) {
            // TODO: provide better exception messaging
            $result = json_decode($response->getBody()->getContents());
            throw new ImportException($result);
        }
        return json_decode($response->getBody(), true)['data'];
    }
}
