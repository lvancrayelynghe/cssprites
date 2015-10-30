<?php

namespace CSSPrites\SpriteProcessor;

use CSSPrites\AbstractConfigurable;
use CSSPrites\ImageProcessor\ImageProcessorInterface;

abstract class AbstractSpriteProcessor extends AbstractConfigurable
{
    protected $filepath;
    protected $overwrite = false;

    protected $image;
    protected $processor;

    protected $imageProcessor;
    protected $images;

    protected $spaces     = 0;
    protected $background = null;

    public function __construct(ImageProcessorInterface $imageProcessor)
    {
        $this->imageProcessor = $imageProcessor;
    }

    public function overwrite($value)
    {
        $this->overwrite = $value;

        return $this;
    }

    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;

        return $this;
    }

    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    public function setSpaces($spaces)
    {
        $this->spaces = (int) $spaces;

        return $this;
    }

    public function save()
    {
        if (empty($this->filepath)) {
            throw new \Exception('No file path set on the Processor');
        }

        if ($this->overwrite === false && file_exists($this->filepath)) {
            throw new \Exception('File "'.$this->filepath.'" already exists and overwriting is disabled in the Processor');
        }

        if (file_exists($this->filepath)) {
            unlink($this->filepath);
        }

        return $this->image->save($this->filepath);
    }
}
