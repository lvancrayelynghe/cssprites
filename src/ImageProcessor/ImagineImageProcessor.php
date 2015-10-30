<?php

namespace CSSPrites\ImageProcessor;

use Imagine\Gd\Imagine as ImagineGd;
use Imagine\Gmagick\Imagine as ImagineGmagick;
use Imagine\Image\Box;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine as ImagineImagick;

class ImagineImageProcessor extends AbstractImageProcessor implements ImageProcessorInterface
{
    public function setConfig($config)
    {
        if ($config['driver'] == 'gd') {
            $this->manager = new ImagineGd();
        } elseif ($config['driver'] == 'imagick') {
            $this->manager = new ImagineImagick();
        } elseif ($config['driver'] == 'gmagick') {
            $this->manager = new ImagineGmagick();
        } else {
            throw new \Exception('Unknown Imagine Driver "'.$config['driver'].'"');
        }
    }

    public function create($width, $height, $background = null)
    {
        $palette = new RGB();
        $color   = is_null($background) ? $palette->color('#fff', 0) : $palette->color($background, 100);

        $size  = new Box($width, $height);

        $newImage = new self();
        $newImage->setImage($this->manager->create($size, $color));

        return $newImage;
    }

    public function load($path)
    {
        $newImage = new self();
        $newImage->setImage($this->manager->open($path));

        return $newImage;
    }

    public function save($path)
    {
        return $this->image->save($path);
    }

    public function insert(ImageProcessorInterface $image, $x = 0, $y = 0)
    {
        return $this->image->paste($image->getImage(), new Point($x, $y));
    }

    public function getWidth()
    {
        if (is_null($this->image)) {
            throw new \Exception('No image created / loaded');
        }

        $size = $this->image->getSize();

        return $size->getWidth();
    }

    public function getHeight()
    {
        if (is_null($this->image)) {
            throw new \Exception('No image created / loaded');
        }

        $size = $this->image->getSize();

        return $size->getHeight();
    }
}
