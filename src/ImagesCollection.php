<?php

namespace CSSPrites;

use Symfony\Component\Finder\Finder;

class ImagesCollection
{
    protected $processor;
    protected $directory;

    protected $mask   = '';
    protected $ignore = '';

    protected $images = null;

    public function __construct($processor, $directory, $mask, $ignore)
    {
        $this->processor = $processor;
        $this->directory = $directory;
        $this->mask      = $mask;
        $this->ignore    = $ignore;

        $this->populateImages();
    }

    public function count()
    {
        return count($this->images);
    }

    public function sort($order)
    {
        if ($order == 'biggest') {
            usort($this->images, function (Image $a, Image $b) {
                if ($a->getSurface() == $b->getSurface()) {
                    // Add some tricks for https://bugs.php.net/bug.php?id=69158
                    return strcmp($b->getSimpleName(), $a->getSimpleName());
                }

                return ($a->getSurface() > $b->getSurface()) ? -1 : 1;
            });
        }

        return $this;
    }

    public function get($key = null)
    {
        if (!is_null($key)) {
            return $this->images[$key];
        }

        return $this->images;
    }

    public function maxWidth()
    {
        $max = 0;
        foreach ($this->images as $image) {
            $max = max($max, $image->getWidth());
        }

        return $max;
    }

    public function maxHeight()
    {
        $max = 0;
        foreach ($this->images as $image) {
            $max = max($max, $image->getHeight());
        }

        return $max;
    }

    protected function populateImages()
    {
        $files = Finder::create()->in($this->directory)->name($this->mask)->notName($this->ignore)->depth(0)->files();

        if ($files->count() <= 0) {
            throw new \Exception('No image found in the directory '.$this->directory);
        }

        foreach ($files as $file) {
            $this->images[] = new Image($file->getRealpath(), $this->processor->load($file->getRealpath()));
        }

        return $this;
    }
}
