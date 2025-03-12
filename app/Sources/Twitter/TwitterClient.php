<?php

namespace App\Sources\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Twitter REST API v1.1 client
 */
class TwitterClient
{
    protected \Abraham\TwitterOAuth\TwitterOAuth $client;

    /**
     * @link
     */
    public function __construct()
    {
        $consumerKey = config('services.twitter.consumer_key');
        $consumerSecret = config('services.twitter.consumer_secret');
        $this->client = new TwitterOAuth($consumerKey, $consumerSecret);
    }

    /**
     * @link https://developer.twitter.com/en/docs/twitter-api/v1/accounts-and-users/follow-search-get-users/api-reference/get-users-show
     */
    public function getUser(string $id, string $field = 'screen_name'): object
    {
        return $this->client->get('/users/show/', [$field => $id]);
    }

    /**
     * @link https://developer.twitter.com/en/docs/twitter-api/v1/tweets/post-and-engage/api-reference/get-statuses-show-id
     */
    public function getStatus(string $id): object
    {
        return $this->client->get("/statuses/show/$id", ['tweet_mode' => 'extended']);
    }
}
