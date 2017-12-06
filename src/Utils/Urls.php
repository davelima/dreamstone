<?php
namespace App\Utils;

class Urls
{
    /**
     * Converts a URL into a slug
     * Based on Matheo Spinelli code <matteo@cubiq.org>
     *
     * @param string $string
     * @return string
     */
    public static function slugify($string)
    {
        $url = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $url = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $url);
        $url = strtolower(trim($url, '-'));
        $url = preg_replace("/[\/_|+ -]+/", '-', $url);

        return $url;
    }
}

