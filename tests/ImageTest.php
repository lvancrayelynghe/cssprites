<?php

use CSSPrites\ImageProcessor\ImagineImageProcessor;
use CSSPrites\ImagesCollection;

class ImageTest extends PHPUnit_Framework_TestCase
{
    protected $processor;
    protected $directory;
    protected $mask;
    protected $filename;

    protected $collection;

    public function __construct()
    {
        $this->processor = new ImagineImageProcessor();
        $this->processor->setConfig(['driver' => 'gd']);

        $this->directory = './tests/stubs';
        $this->mask      = 'test-*.png';
        $this->filename  = 'sprite.png';

        $this->collection = new ImagesCollection(
            $this->processor,
            $this->directory,
            $this->mask,
            $this->filename
        );
    }

    public function testGetSimpleName()
    {
        $image = $this->collection->get(0);

        $this->assertSame('test-ico-cross-32', $image->getSimpleName());
    }

    public function testGetFilepath()
    {
        $image = $this->collection->get(0);
        $path  = realpath('./tests/stubs').'/'.$image->getSimpleName().'.png';

        $this->assertSame($path, $image->getFilepath());
    }

    public function testGetImage()
    {
        $image = $this->collection->get(0);

        $this->assertInstanceOf('CSSPrites\ImageProcessor\ImagineImageProcessor', $image->getImage());
    }

    public function testGetX()
    {
        $image = $this->collection->get(0);

        $this->assertSame(null, $image->getX());
    }

    public function testGetY()
    {
        $image = $this->collection->get(0);

        $this->assertSame(null, $image->getY());
    }

    public function testGetSurface()
    {
        $image = $this->collection->get(0);

        $this->assertSame(32 * 32, $image->getSurface());
    }

    public function testSetX()
    {
        $image  = $this->collection->get(0);
        $return = $image->setX(12);

        $this->assertInstanceOf('CSSPrites\Image', $return);
        $this->assertSame(12, $return->getX());
    }

    public function testSetY()
    {
        $image  = $this->collection->get(0);
        $return = $image->setY(24);

        $this->assertInstanceOf('CSSPrites\Image', $return);

        $this->assertSame(24, $return->getY());
    }
}
