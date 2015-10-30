<?php

namespace CSSPrites\ImageProcessor;

abstract class AbstractImageProcessor
{
    protected $manager;
    protected $image = null;

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }
}
