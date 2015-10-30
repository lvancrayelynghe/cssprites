<?php

namespace CSSPrites;

class Image
{
    protected $filepath;
    protected $image;

    protected $width;
    protected $height;

    protected $x = null;
    protected $y = null;

    public function __construct($filepath, $image)
    {
        $this->filepath = $filepath;
        $this->image    = $image;
        $this->width    = (int) $this->image->getWidth();
        $this->height   = (int) $this->image->getHeight();
    }

    public function getSimpleName()
    {
        return pathinfo($this->filepath, PATHINFO_FILENAME);
    }

    public function getFilepath()
    {
        return $this->filepath;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getX()
    {
        return $this->x;
    }

    public function getY()
    {
        return $this->y;
    }

    public function getSurface()
    {
        return $this->width * $this->height;
    }

    public function setX($x)
    {
        $this->x = (int) $x;

        return $this;
    }

    public function setY($y)
    {
        $this->y = (int) $y;

        return $this;
    }
}
