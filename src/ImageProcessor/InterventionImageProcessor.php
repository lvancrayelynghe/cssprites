<?php

namespace CSSPrites\ImageProcessor;

use Intervention\Image\ImageManager;

/**
 * @codeCoverageIgnore
 */
class InterventionImageProcessor extends AbstractImageProcessor implements ImageProcessorInterface
{
    public function __construct()
    {
        $this->manager = new ImageManager();
    }

    public function setConfig($config)
    {
        $this->manager->configure($config);
    }

    public function create($width, $height, $background = null)
    {
        $newImage = new self();
        $newImage->setImage($this->manager->canvas($width, $height, $background));

        return $newImage;
    }

    public function load($path)
    {
        $newImage = new self();
        $newImage->setImage($this->manager->make($path));

        return $newImage;
    }

    public function save($path)
    {
        return $this->image->save($path);
    }

    public function insert(ImageProcessorInterface $image, $x = 0, $y = 0)
    {
        return $this->image->insert($image->getImage(), 'top-left', $x, $y);
    }

    public function getWidth()
    {
        if (is_null($this->image)) {
            throw new \Exception('No image created / loaded');
        }

        return $this->image->width();
    }

    public function getHeight()
    {
        if (is_null($this->image)) {
            throw new \Exception('No image created / loaded');
        }

        return $this->image->height();
    }
}
