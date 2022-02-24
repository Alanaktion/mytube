<?php

namespace App\Translation;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader as LaravelTranslationFileLoader;
use RuntimeException;

/**
 * Adapted from original in the overtrue/laravel-lang package
 *
 * @author 安正超
 */
class FileLoader extends LaravelTranslationFileLoader
{
    /**
     * Create a new file loader instance.
     *
     * @param string $path
     * @param string[] $paths
     */
    public function __construct(Filesystem $files, $path, protected $paths = [])
    {
        parent::__construct($files, $path);
    }

    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     * @return array<string, string>
     */
    public function load($locale, $group, $namespace = null): array
    {
        if ($group === '*' && $namespace === '*') {
            return $this->loadJsonPaths($locale);
        }

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
     * @return array<string, string>
     */
    protected function loadPath($path, $locale, $group): array
    {
        $result = parent::loadPath($path, $locale, $group);

        if (empty($result) && Str::contains($locale, '-')) {
            return parent::loadPath($path, strstr($locale, '-', true), $group);
        }

        return $result;
    }

    /**
     * Load JSON files from locale-specific subdirectories.
     *
     * @param string $locale
     * @return string[]
     */
    protected function loadJsonPaths($locale): array
    {
        $output = parent::loadJsonPaths($locale);

        foreach ($this->jsonPaths as $path) {
            if ($this->files->exists($full = "$path/$locale/$locale.json")) {
                $decoded = json_decode($this->files->get($full), true);

                if ($decoded === null || json_last_error() !== JSON_ERROR_NONE) {
                    throw new RuntimeException("Translation file [{$full}] contains an invalid JSON structure.");
                }

                $output = array_merge($output, $decoded);
            }
        }

        return $output;
    }
}
