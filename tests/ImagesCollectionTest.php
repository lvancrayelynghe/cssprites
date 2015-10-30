<?php

use CSSPrites\ImageProcessor\ImagineImageProcessor;
use CSSPrites\ImagesCollection;

class ImagesCollectionTest extends PHPUnit_Framework_TestCase
{
    protected $processor;
    protected $directory;
    protected $mask;
    protected $filename;

    public function __construct()
    {
        $this->processor = new ImagineImageProcessor();
        $this->processor->setConfig(['driver' => 'gd']);

        $this->directory = './tests/stubs';
        $this->mask      = 'test-*.png';
        $this->filename  = 'sprite.png';
    }

    public function testConstructor()
    {
        $collection = new ImagesCollection(
            $this->processor,
            $this->directory,
            $this->mask,
            $this->filename
        );

        $this->assertInstanceOf('CSSPrites\ImagesCollection', $collection);
    }

    public function testCount()
    {
        $collection = new ImagesCollection(
            $this->processor,
            $this->directory,
            $this->mask,
            $this->filename
        );

        $this->assertSame(3, $collection->count());
    }

    public function testSort()
    {
        $collection = new ImagesCollection(
            $this->processor,
            $this->directory,
            $this->mask,
            $this->filename
        );
        $return = $collection->sort('biggest');

        $first  = $collection->get(0);
        $second = $collection->get(1);

        $this->assertSame(100, $first->getWidth());
        $this->assertSame(200, $first->getHeight());
        $this->assertSame(32, $second->getWidth());
        $this->assertSame(32, $second->getHeight());
        $this->assertInstanceOf('CSSPrites\ImagesCollection', $return);
    }

    public function testGetOne()
    {
        $collection = new ImagesCollection(
            $this->processor,
            $this->directory,
            $this->mask,
            $this->filename
        );

        $this->assertInstanceOf('CSSPrites\Image', $collection->get(0));
    }

    public function testGetAll()
    {
        $collection = new ImagesCollection(
            $this->processor,
            $this->directory,
            $this->mask,
            $this->filename
        );

        $this->assertInternalType('array', $collection->get());
    }

    public function testMaxWidth()
    {
        $collection = new ImagesCollection(
            $this->processor,
            $this->directory,
            $this->mask,
            $this->filename
        );

        $this->assertSame(100, $collection->maxWidth());
    }

    public function testMaxHeight()
    {
        $collection = new ImagesCollection(
            $this->processor,
            $this->directory,
            $this->mask,
            $this->filename
        );

        $this->assertSame(200, $collection->maxHeight());
    }

    public function testPopulateException()
    {
        $this->setExpectedException('Exception', 'No image found in the directory ./tests/stubs');

        $collection = new ImagesCollection(
            $this->processor,
            $this->directory,
            '*.test',
            $this->filename
        );
    }
}
