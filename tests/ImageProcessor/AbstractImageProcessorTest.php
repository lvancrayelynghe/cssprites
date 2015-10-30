<?php

use CSSPrites\ImageProcessor\ImagineImageProcessor;

class AbstractImageProcessorTest extends PHPUnit_Framework_TestCase
{
    public function testGetImage()
    {
        $p = new ImagineImageProcessor();
        $p->setConfig(['driver' => 'gd']);

        $fake = new \stdClass();
        $p->setImage($fake);

        $get = $p->getImage();

        $this->assertSame($get, $fake);
    }
}
