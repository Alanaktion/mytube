<?php

use App\Exceptions\InvalidSourceException;
use App\Sources\Source;

if (!function_exists('source')) {
    function source(string $type): Source
    {
        $sources = app()->tagged('sources');
        foreach ($sources as $source) {
            /** @var \App\Sources\Source $source */
            if ($source->getSourceType() === $type) {
                return $source;
            }
        }
        throw new InvalidSourceException('Invalid source type ' . $type);
    }
}

if (!function_exists('format_description')) {
    /**
     * Convert URLs and hashtags to links, escaping HTML
     */
    function format_description(string $description): string
    {
        $class = 'text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300';
        $str = htmlspecialchars($description);
        $str = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" class="' . $class . '" target="_blank" rel="noopener noreferrer">$1</a>',
            $str
        );
        $str = preg_replace(
            '/(^|\s)#([0-9a-z_-]+)/i',
            '$1<a href="/search?q=$2" class="' . $class . '">#$2</a>',
            $str
        );
        return $str;
    }
}
