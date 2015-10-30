<?php

use CSSPrites\Configuration;
use CSSPrites\ImageProcessor\ImagineImageProcessor;
use CSSPrites\ImagesCollection;
use CSSPrites\SpriteProcessor\HorizontalSpriteProcessor;

class HorizontalSpriteProcessorTest extends PHPUnit_Framework_TestCase
{
    protected $processor;
    protected $directory;
    protected $mask;
    protected $filename;

    protected $config;

    protected $collection;

    public function __construct()
    {
        $this->config = new Configuration();
        $this->config->load('./tests/stubs/test-cssprites-correct-config.json');

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

    public function testProcess()
    {
        $pathL = './tests/stubs/spritetest-horizontal-correct.png';
        $pathS = './tests/stubs/spritetest-horizontal-ouput.png';

        $sprite = new HorizontalSpriteProcessor($this->processor);
        $sprite->configure($this->config->get('sprite'))
            ->overwrite(true)
            ->setFilepath($pathS)
            ->setSpaces(2)
            ->setImages($this->collection)
            ->process();

        $saved = $sprite->save();

        $this->assertInstanceOf('CSSPrites\SpriteProcessor\HorizontalSpriteProcessor', $sprite);
        $this->assertInstanceOf('Imagine\Gd\Image', $saved);

        $this->assertFileEquals($pathS, $pathL);
        unlink($pathS);
    }
}
