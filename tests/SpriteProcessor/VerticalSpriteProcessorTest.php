<?php

use CSSPrites\Configuration;
use CSSPrites\ImageProcessor\ImagineImageProcessor;
use CSSPrites\ImagesCollection;
use CSSPrites\SpriteProcessor\VerticalSpriteProcessor;

class VerticalSpriteProcessorTest extends AbstractBaseTest
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
        $pathL = './tests/stubs/spritetest-vertical-correct.png';
        $pathS = './tests/stubs/spritetest-vertical-ouput.png';

        $sprite = new VerticalSpriteProcessor($this->processor);
        $sprite->configure($this->config->get('sprite'))
            ->overwrite(true)
            ->setFilepath($pathS)
            ->setSpaces(2)
            ->setImages($this->collection)
            ->process();

        $saved = $sprite->save();

        $this->assertInstanceOf('CSSPrites\SpriteProcessor\VerticalSpriteProcessor', $sprite);
        $this->assertInstanceOf('Imagine\Gd\Image', $saved);

        $this->assertImageEquals($pathS, $pathL);
        unlink($pathS);
    }
}
