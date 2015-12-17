<?php

use CSSPrites\Generator\CSSGenerator;
use CSSPrites\Generator\HTMLGenerator;

class HTMLGeneratorTest extends AbstractBaseTest
{
    public function testTag()
    {
        $tag = 'span';

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $return        = $htmlgenerator->setTag($tag);

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $return);
    }

    public function testTemplate()
    {
        $template = '<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="{{stylesheet}}"></head><body>{{content}}</body></html>';

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $return        = $htmlgenerator->setTemplate($template);

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $return);
    }

    public function testCssGenerator()
    {
        $cssgenerator  = new CSSGenerator($this->slugifier);
        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $return        = $htmlgenerator->setCSSGenerator($cssgenerator);

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $return);
    }

    public function testAddLine()
    {
        $image = 'test-output';

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $htmlgenerator->setTag('span');
        $return = $htmlgenerator->addLine($image);

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $return);
    }

    public function testProcessEmptyException()
    {
        $this->setExpectedException('Exception', 'CSSGenerator not set');

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $htmlgenerator->setTag('span');
        $return = $htmlgenerator->process();
    }

    public function testProcessEmpty()
    {
        $cssgenerator  = new CSSGenerator($this->slugifier);
        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $htmlgenerator->setCSSGenerator($cssgenerator);
        $htmlgenerator->setTag('span');
        $return = $htmlgenerator->process();

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertSame('', $return);
    }

    public function testProcessNoLine()
    {
        $cssgenerator = new CSSGenerator($this->slugifier);
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('testSelector');
        $cssgenerator->setPrefix('testPrefix');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $cssgenerator->setFilepath('./tests/stubs/test-css-generator-output.css');

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $htmlgenerator->setTemplate('<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="{{stylesheet}}"></head><body>{{content}}</body></html>');
        $htmlgenerator->setCSSGenerator($cssgenerator);
        $htmlgenerator->setTag('span');
        $return = $htmlgenerator->process();

        $expected = '<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="test-css-generator-output.css"></head><body></body></html>';

        $return   = preg_replace('!\s+!', ' ', $return);
        $expected = preg_replace('!\s+!', ' ', $expected);

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertSame($expected, $return);
    }

    public function testProcessOneLine()
    {
        $cssgenerator = new CSSGenerator($this->slugifier);
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('testSelector');
        $cssgenerator->setPrefix('testPrefix');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $cssgenerator->setFilepath('./tests/stubs/test-css-generator-output.css');
        $cssgenerator->addLine('test-input', 1, 2, 3, 4);

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $htmlgenerator->setTemplate('<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="{{stylesheet}}"></head><body>{{content}}</body></html>');
        $htmlgenerator->setCSSGenerator($cssgenerator);
        $htmlgenerator->setTag('span');
        $htmlgenerator->addLine('test-input');
        $return = $htmlgenerator->process();

        $expected = '<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="test-css-generator-output.css"></head><body> <span class="testSelector testPrefix-test-input"></span></body></html>';

        $return   = preg_replace('!\s+!', ' ', $return);
        $expected = preg_replace('!\s+!', ' ', $expected);

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertSame($expected, $return);
    }

    public function testProcessOneLineSpecialChars()
    {
        $cssgenerator = new CSSGenerator($this->slugifier);
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('test Sélectór.');
        $cssgenerator->setPrefix('test Prèfîx.');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $cssgenerator->setFilepath('./tests/stubs/test-css-generator-output.css');
        $cssgenerator->addLine('test. inputŒЙ', 1, 2, 3, 4);

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $htmlgenerator->setTemplate('<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="{{stylesheet}}"></head><body>{{content}}</body></html>');
        $htmlgenerator->setCSSGenerator($cssgenerator);
        $htmlgenerator->setTag('span');
        $htmlgenerator->addLine('test. inputŒЙ');
        $return = $htmlgenerator->process();

        $expected = '<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="test-css-generator-output.css"></head><body> <span class="test-Selector test-Prefix-test-inputOEJ"></span></body></html>';

        $return   = preg_replace('!\s+!', ' ', $return);
        $expected = preg_replace('!\s+!', ' ', $expected);

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertSame($expected, $return);
    }

    public function testProcessMultiLine()
    {
        $cssgenerator = new CSSGenerator($this->slugifier);
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('testSelector');
        $cssgenerator->setPrefix('testPrefix');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $cssgenerator->setFilepath('./tests/stubs/test-css-generator-output.css');
        $cssgenerator->addLine('test-input', 1, 2, 3, 4);
        $cssgenerator->addLine('test-multi-input', -10, -20, -30, -40);

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $htmlgenerator->setTemplate('<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="{{stylesheet}}"></head><body>{{content}}</body></html>');
        $htmlgenerator->setCSSGenerator($cssgenerator);
        $htmlgenerator->setTag('span');
        $htmlgenerator->addLine('test-input');
        $htmlgenerator->addLine('test-multi-input');
        $return = $htmlgenerator->process();

        $expected = '<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="test-css-generator-output.css"></head><body> <span class="testSelector testPrefix-test-input"></span> <span class="testSelector testPrefix-test-multi-input"></span></body></html>';

        $return   = preg_replace('!\s+!', ' ', $return);
        $expected = preg_replace('!\s+!', ' ', $expected);

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertSame($expected, $return);
    }

    public function testSaveExceptionCssGenerator()
    {
        $this->setExpectedException('Exception', 'CSSGenerator not set');

        $cssgenerator = new CSSGenerator($this->slugifier);
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('testSelector');
        $cssgenerator->setPrefix('testPrefix');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $cssgenerator->setFilepath('./tests/stubs/test-css-generator-output.css');
        $cssgenerator->addLine('test-input', 1, 2, 3, 4);
        $cssgenerator->addLine('test-multi-input', -10, -20, -30, -40);

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $htmlgenerator->setTemplate('<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="{{stylesheet}}"></head><body>{{content}}</body></html>');
        // $htmlgenerator->setCSSGenerator($cssgenerator);
        $htmlgenerator->setTag('span');
        $htmlgenerator->addLine('test-input');
        $htmlgenerator->addLine('test-multi-input');
        $return = $htmlgenerator->save();
    }

    public function testSave()
    {
        $path = './tests/stubs/test-html-generator-output.html';

        $cssgenerator = new CSSGenerator($this->slugifier);
        $cssgenerator->setImage('test-output.png');
        $cssgenerator->setSelector('testSelector');
        $cssgenerator->setPrefix('testPrefix');
        $cssgenerator->setMainLine('.{{selector}} {display:inline-block; background-image:url({{image}})}');
        $cssgenerator->setSpriteLine('.{{selector}}.{{prefix}}-{{filename}} {background-position:{{x}}px {{y}}px; width:{{w}}px; height:{{h}}px; }');
        $cssgenerator->setFilepath('./tests/stubs/test-css-generator-output.css');
        $cssgenerator->addLine('test-input', 1, 2, 3, 4);
        $cssgenerator->addLine('test-multi-input', -10, -20, -30, -40);

        $htmlgenerator = new HTMLGenerator($this->slugifier);
        $htmlgenerator->setTemplate('<!DOCTYPE html><html><head><title>SpriteTest</title><link rel="stylesheet" type="text/css" href="{{stylesheet}}"></head><body>{{content}}</body></html>');
        $htmlgenerator->setCSSGenerator($cssgenerator);
        $htmlgenerator->setTag('span');
        $htmlgenerator->addLine('test-input');
        $htmlgenerator->addLine('test-multi-input');
        $htmlgenerator->setFilepath($path);
        $htmlgenerator->overwrite(true);
        $htmlgenerator->save();

        $this->assertInstanceOf('CSSPrites\Generator\HTMLGenerator', $htmlgenerator);
        $this->assertFileExists($path);
        $this->assertFileEquals('./tests/stubs/test-html-generator-output-expected.html', $path);
        unlink($path);
    }
}
