<?php

namespace App\Providers;

use Illuminate\Translation\TranslationServiceProvider;

class ServiceProvider extends TranslationServiceProvider
{
    /**
     * Register the translation line loader.
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            $paths = [
                base_path('vendor/laravel-lang/lang/src/'),
            ];

            if ($this->inLumen) {
                $this->app['path.lang'] = base_path('vendor/laravel/lumen-framework/resources/lang');
                array_push($paths, base_path('resources/lang/'));
            }

            $loader = new FileLoader($app['files'], $app['path.lang'], $paths);

            if (\is_callable([$loader, 'addJsonPath'])) {
                $loader->addJsonPath(base_path('vendor/laravel-lang/lang/json/'));
            }

            return $loader;
        });
    }
}
