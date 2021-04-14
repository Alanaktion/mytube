<?php

namespace App\Translation;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader as LaravelTranslationFileLoader;

/**
 * Adapted from original in the overtrue/laravel-lang package
 *
 * @author 安正超
 */
class FileLoader extends LaravelTranslationFileLoader
{
    /**
     * @var array
     */
    protected $paths;

    /**
     * Create a new file loader instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param array $path
     * @param array $paths
     */
    public function __construct(Filesystem $files, $path, $paths = [])
    {
        $this->paths = $paths;

        parent::__construct($files, $path);
    }

    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        $defaults = [];

        foreach ($this->paths as $path) {
            $defaults = array_replace_recursive($defaults, $this->loadPath($path, $locale, $group));
        }

        return array_replace_recursive($defaults, parent::load($locale, $group, $namespace));
    }

    /**
     * Fall back to base locale (i.e. de) if a countries specific locale (i.e. de-CH) is not available.
     *
     * @param string $path
     * @param string $locale
     * @param string $group
     *
     * @return array
     */
    protected function loadPath($path, $locale, $group)
    {
        $result = parent::loadPath($path, $locale, $group);

        if (empty($result) && Str::contains($locale, '-')) {
            return parent::loadPath($path, strstr($locale, '-', true), $group);
        }

        return $result;
    }
}
