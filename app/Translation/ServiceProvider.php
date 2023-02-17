<?php

namespace App\Translation;

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
                $app['path.lang'],
                base_path('vendor/laravel-lang/lang/locales'),
            ];

            $loader = new FileLoader($app['files'], $paths);
            $loader->addJsonPath(base_path('vendor/laravel-lang/lang/locales'));

            return $loader;
        });
    }
}
