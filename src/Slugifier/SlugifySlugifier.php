<?php

namespace CSSPrites\Slugifier;

use URLify;

class SlugifySlugifier implements SlugifierInterface
{
    /**
     * {@inheritdoc}
     */
    public function slugify($text)
    {
        $text = URLify::downcode($text);
        $text = preg_replace('/\b('.implode('|', URLify::$remove_list).')\b/i', '', $text);

        $text = preg_replace('/[^\s_\-a-zA-Z0-9]/u', '', $text); // remove unneeded chars
        $text = str_replace('_', ' ', $text);                    // treat underscores as spaces
        $text = preg_replace('/^\s+|\s+$/u', '', $text);         // trim leading/trailing spaces
        $text = preg_replace('/[-\s]+/u', '-', $text);           // convert spaces to hyphens
        $text = trim($text, '-');                                // trim to first $length chars

        return $text;
    }
}
