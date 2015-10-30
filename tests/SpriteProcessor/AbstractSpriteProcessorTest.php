<?php

use CSSPrites\Configuration;
use CSSPrites\ImageProcessor\ImagineImageProcessor;
use CSSPrites\ImagesCollection;
use CSSPrites\SpriteProcessor\HorizontalSpriteProcessor;

class AbstractSpriteProcessorTest extends PHPUnit_Framework_TestCase
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

    public function testProcessPathException()
    {
        $this->setExpectedException('Exception', 'No file path set on the Processor');

        $pathL = './tests/stubs/spritetest-horizontal-correct.png';
        $pathS = './tests/stubs/spritetest-horizontal-ouput.png';

        $sprite = new HorizontalSpriteProcessor($this->processor);
        $sprite
            ->setSpaces(2)
            ->setImages($this->collection)
            ->process();

        $saved = $sprite->save();
    }

    public function testProcessOverwriteException()
    {
        $this->setExpectedException('Exception', 'File "./tests/stubs/spritetest-horizontal-ouput.png" already exists and overwriting is disabled in the Processor');

        $pathL = './tests/stubs/spritetest-horizontal-correct.png';
        $pathS = './tests/stubs/spritetest-horizontal-ouput.png';

        touch($pathS);

        $sprite = new HorizontalSpriteProcessor($this->processor);
        $sprite->configure($this->config->get('sprite'))
            ->setFilepath($pathS)
            ->overwrite(false)
            ->setSpaces(2)
            ->setImages($this->collection)
            ->process();

        $saved = $sprite->save();
    }

    public function testProcessUnlink()
    {
        $pathL = './tests/stubs/spritetest-horizontal-correct.png';
        $pathS = './tests/stubs/spritetest-horizontal-ouput.png';

        touch($pathS);

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
