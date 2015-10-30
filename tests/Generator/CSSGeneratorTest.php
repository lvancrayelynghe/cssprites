<?php

use CSSPrites\Generator\CSSGenerator;

class CSSGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testImage()
    {
        $image = 'test-output.png';

        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->setImage($image);

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
    }

    public function testSelector()
    {
        $selector = 'testSelector';

        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->setSelector($selector);

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
        $this->assertSame($cssgenerator->getSelector(), $selector);
    }

    public function testPrefix()
    {
        $prefix = 'testPrefix';

        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->setPrefix($prefix);

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
        $this->assertSame($cssgenerator->getPrefix(), $prefix);
    }

    public function testMainLine()
    {
        $mainLine = '.{{selector}} {display:inline-block; background-image:url({{image}})}';

        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->setMainLine($mainLine);

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
    }

    public function testSpriteLine()
    {
        $spriteLine = '.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }';

        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->setSpriteLine($spriteLine);

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
    }

    public function testAddLine()
    {
        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->addLine('test-input', 1, 2, 3, 4);

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
    }

    public function testProcessEmpty()
    {
        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->process();

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertSame('', $return);
    }

    public function testProcessNoMainLine()
    {
        $cssgenerator = new CSSGenerator();
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->addLine('test-input', 1, 2, 3, 4);
        $return = $cssgenerator->process();

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertSame('', $return);
    }

    public function testProcessNoLine()
    {
        $cssgenerator = new CSSGenerator();
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('testSelector');
        $cssgenerator->setPrefix('testPrefix');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $return = $cssgenerator->process();

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertSame('.testSelector {display:inline-block; background-image:url(test-output.png)}', $return);
    }

    public function testProcessOneLine()
    {
        $cssgenerator = new CSSGenerator();
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('testSelector');
        $cssgenerator->setPrefix('testPrefix');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $cssgenerator->addLine('test-input', 1, 2, 3, 4);
        $return = $cssgenerator->process();

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertSame('.testSelector {display:inline-block; background-image:url(test-output.png)}.testSelector.testPrefix-test-input {background-position:1px 2px; width:3px; height:4px; }', $return);
    }

    public function testProcessMultiLine()
    {
        $cssgenerator = new CSSGenerator();
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('testSelector');
        $cssgenerator->setPrefix('testPrefix');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $cssgenerator->addLine('test-input', 1, 2, 3, 4);
        $cssgenerator->addLine('test-multi-input', -10, -20, -30, -40);
        $return = $cssgenerator->process();

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertSame('.testSelector {display:inline-block; background-image:url(test-output.png)}.testSelector.testPrefix-test-input {background-position:1px 2px; width:3px; height:4px; }.testSelector.testPrefix-test-multi-input {background-position:-10px -20px; width:-30px; height:-40px; }', $return);
    }

    public function testSave()
    {
        $path = './tests/stubs/test-css-generator-output.css';

        $cssgenerator = new CSSGenerator();
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('testSelector');
        $cssgenerator->setPrefix('testPrefix');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $cssgenerator->addLine('test-input', 1, 2, 3, 4);
        $cssgenerator->addLine('test-multi-input', -10, -20, -30, -40);
        $cssgenerator->setFilepath($path);
        $cssgenerator->overwrite(true);
        $cssgenerator->save();

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertFileExists($path);
        $this->assertFileEquals('./tests/stubs/test-css-generator-output-expected.css', $path);
        unlink($path);
    }
}
