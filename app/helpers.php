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
