<?php

namespace App\Providers;

use App\Sources\Floatplane\FloatplaneSource;
use App\Sources\Twitch\TwitchSource;
use App\Sources\Twitter\TwitterSource;
use App\Sources\YouTube\YouTubeSource;
use Google\Client as GoogleClient;
use Google\Service\YouTube;
use Illuminate\Database\Eloquent\Model;
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
        $this->app->singleton(YouTube::class, function ($app) {
            $client = new GoogleClient();
            $client->setApplicationName('MyTube');
            $client->setScopes([
                'https://www.googleapis.com/auth/youtube.readonly',
            ]);
            $client->setDeveloperKey(config('services.youtube.key'));
            return new YouTube($client);
        });

        $this->app->tag([
            FloatplaneSource::class,
            TwitchSource::class,
            TwitterSource::class,
            YouTubeSource::class,
        ], 'sources');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(!app()->isProduction());
    }
}
