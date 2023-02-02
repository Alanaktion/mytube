<?php

namespace App\Providers;

use App\Sources\Floatplane\FloatplaneSource;
use App\Sources\Twitch\TwitchSource;
use App\Sources\Twitter\TwitterSource;
use App\Sources\YouTube\YouTubeSource;
use Google\Client as GoogleClient;
use Google\Service\YouTube;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(YouTube::class, function (): \Google\Service\YouTube {
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

        $this->initPlugins();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(!app()->isProduction());

        Blade::directive('description', fn($expression) => "<?php echo format_description($expression); ?>");
    }

    /**
     * Initialize any installed plugins
     */
    private function initPlugins(): void
    {
        $items = glob(dirname(__DIR__, 2) . '/plugins/*', GLOB_ONLYDIR);
        foreach ($items as $item) {
            include_once "$item/init.php";
        }
    }
}
