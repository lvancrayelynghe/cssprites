<?php

use CSSPrites\ImageProcessor\ImagineImageProcessor;

class ImagineImageProcessorTest extends PHPUnit_Framework_TestCase
{
    public function testSetConfigGd()
    {
        $p      = new ImagineImageProcessor();
        $return = $p->setConfig(['driver' => 'gd']);

        $this->assertInstanceOf('CSSPrites\ImageProcessor\ImagineImageProcessor', $p);
        $this->assertSame(null, $return);
    }

    public function testSetConfigImagick()
    {
        $this->setExpectedException('Imagine\Exception\RuntimeException', 'Imagick not installed');

        $p      = new ImagineImageProcessor();
        $return = $p->setConfig(['driver' => 'imagick']);

        $this->assertInstanceOf('CSSPrites\ImageProcessor\ImagineImageProcessor', $p);
        $this->assertSame(null, $return);
    }

    public function testSetConfigGmagick()
    {
        $this->setExpectedException('Imagine\Exception\RuntimeException', 'Gmagick not installed');

        $p      = new ImagineImageProcessor();
        $return = $p->setConfig(['driver' => 'gmagick']);

        $this->assertInstanceOf('CSSPrites\ImageProcessor\ImagineImageProcessor', $p);
        $this->assertSame(null, $return);
    }

    public function testSetConfigOther()
    {
        $this->setExpectedException('Exception', 'Unknown Imagine Driver "other"');

        $p      = new ImagineImageProcessor();
        $return = $p->setConfig(['driver' => 'other']);

        $this->assertInstanceOf('CSSPrites\ImageProcessor\ImagineImageProcessor', $p);
        $this->assertSame(null, $return);
    }

    public function testGetWidthException()
    {
        $this->setExpectedException('Exception', 'No image created / loaded');

        $p = new ImagineImageProcessor();
        $p->getWidth();
    }

    public function testGetHeightException()
    {
        $this->setExpectedException('Exception', 'No image created / loaded');

        $p = new ImagineImageProcessor();
        $p->getHeight();
    }

    public function testCreate()
    {
        $p      = new ImagineImageProcessor();
        $return = $p->setConfig(['driver' => 'gd']);

        $image = $p->create(100, 200, null);

        $this->assertInstanceOf('CSSPrites\ImageProcessor\ImagineImageProcessor', $image);
        $this->assertSame(100, $image->getWidth());
        $this->assertSame(200, $image->getHeight());
    }

    public function testLoad()
    {
        $path = './tests/stubs/test-ico-plus-32.png';

        $p = new ImagineImageProcessor();
        $p->setConfig(['driver' => 'gd']);

        $return = $p->load($path);

        $this->assertInstanceOf('CSSPrites\ImageProcessor\ImagineImageProcessor', $return);
    }

    public function testSave()
    {
        $pathL = './tests/stubs/test-ico-plus-32.png';
        $pathS = './tests/stubs/test-save-ico-plus-32.png';

        $p = new ImagineImageProcessor();
        $p->setConfig(['driver' => 'gd']);

        $image = $p->load($pathL);
        $image->save($pathS);

        $this->assertInstanceOf('CSSPrites\ImageProcessor\ImagineImageProcessor', $image);
        $this->assertFileEquals($pathS, $pathL);
        unlink($pathS);
    }

    public function testInsert()
    {
        $icoPath = './tests/stubs/test-ico-plus-32.png';

        $pathL = './tests/stubs/test-image-inserted.png';
        $pathS = './tests/stubs/test-save-image-inserted.png';

        $p = new ImagineImageProcessor();
        $p->setConfig(['driver' => 'gd']);

        $image = $p->create(100, 200, null);
        $ico   = $p->load($icoPath);

        $image->insert($ico, 50, 30);
        $image->save($pathS);

        $this->assertInstanceOf('CSSPrites\ImageProcessor\ImagineImageProcessor', $image);
        $this->assertFileEquals($pathS, $pathL);
        unlink($pathS);
    }
}
