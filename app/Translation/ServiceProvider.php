<?php

namespace App\Translation;

use App\Translation\FileLoader;
use Illuminate\Translation\TranslationServiceProvider;

/**
 * Adapted from original in the overtrue/laravel-lang package
 *
 * @author 安正超
 */
class ServiceProvider extends TranslationServiceProvider
{
    /**
     * Register the translation line loader.
     */
    protected function registerLoader(): void
    {
        $this->app->singleton('translation.loader', function ($app): \App\Translation\FileLoader {
            $paths = [
                base_path('vendor/laravel-lang/lang/locales'),
            ];

            $loader = new FileLoader($app['files'], $app['path.lang'], $paths);
            $loader->addJsonPath(base_path('vendor/laravel-lang/lang/locales'));

            return $loader;
        });
    }
}
