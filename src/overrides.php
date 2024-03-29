<?php

use Illuminate\Support\Str;

Str::macro(

/*
 * Extended method from laravel Str class to create slug from unicoded characters
 * @param $title
 * @param string $separator
 * @return string
 */

    "slug_utf8", function ($title, $separator = '-') {

    // Convert all dashes/underscores into separator
    $flip = $separator == '-' ? '_' : '-';

    $title = @preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

    // Remove all characters that are not the separator, letters, numbers, or whitespace.
    $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title));

    // Replace all separator characters and whitespace by a single separator
    $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

    return (string)trim($title, $separator);

});

