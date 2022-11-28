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
        $class = 'text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300';
        $str = htmlspecialchars($description);
        $str = preg_replace(
            '/(https?:\/\/[^\s\]\)]+)/',
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

if (!function_exists('format_time')) {
    /**
     * Convert integer seconds to human-readable time
     */
    function format_time(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds - $hours * 3600) / 60);
        $seconds = $seconds - $hours * 3600 - $minutes * 60;
        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        if ($minutes > 0) {
            return sprintf('%d:%02d', $minutes, $seconds);
        }
        return sprintf('%ds', $seconds);
    }
}
