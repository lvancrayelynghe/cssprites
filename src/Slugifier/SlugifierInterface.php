<?php

namespace CSSPrites\Slugifier;

interface SlugifierInterface
{
    /**
     * Slugify a string (removes accented chars, spaces, etc).
     *
     * @param string $text
     *
     * @return string
     */
    public function slugify($text);
}
