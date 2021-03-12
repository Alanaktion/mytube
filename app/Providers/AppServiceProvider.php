<?php

namespace App\Providers;

use App\Sources\Twitch\TwitchSource;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Google_Service_YouTube', function ($app) {
            $client = new Google_Client();
            $client->setApplicationName('API code samples');
            $client->setScopes([
                'https://www.googleapis.com/auth/youtube.readonly',
            ]);
            $client->setDeveloperKey(config('services.youtube.key'));
            return new Google_Service_YouTube($client);
        });

        $this->app->tag([
            TwitchSource::class,
        ], 'sources');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
