<?php

use CSSPrites\Configuration;

/**
 * Tests for Configuration class.
 *
 * @author Anton Medvedev <anton (at) elfet (dot) ru>
 * @author Luc Vancrayelynghe
 *
 * @version 1.0
 *
 * @license MIT
 */
class ConfigurationTest extends AbstractBaseTest
{
    public function testSet()
    {
        $conf = new Configuration();
        $conf->set('one', 1);
        $this->assertSame(['one' => 1], $conf->getValues());
    }

    public function testSetOverride()
    {
        $conf = new Configuration(['one' => 1]);
        $conf->set('one', 2);
        $this->assertSame(['one' => 2], $conf->getValues());
    }

    public function testSetPath()
    {
        $conf = new Configuration(['one' => ['two' => 1]]);
        $conf->set('one.two', 2);
        $this->assertSame(['one' => ['two' => 2]], $conf->getValues());
    }

    public function testPathAppend()
    {
        $conf = new Configuration(['one' => ['two' => 1]]);
        $conf->set('one.other', 1);
        $this->assertSame(['one' => ['two' => 1, 'other' => 1]], $conf->getValues());
    }

    public function testSetAppend()
    {
        $conf = new Configuration(['one' => ['two' => 1]]);
        $conf->set('two', 2);
        $this->assertSame(['one' => ['two' => 1], 'two' => 2], $conf->getValues());
    }

    public function testSetAppendArray()
    {
        $conf = new Configuration(['one' => ['two' => 1]]);
        $conf->set('one', ['two' => 2]);
        $this->assertSame(['one' => ['two' => 2]], $conf->getValues());
    }

    public function testSetOverrideAndAppend()
    {
        $conf = new Configuration(['one' => ['two' => 1]]);
        $conf->set('one', ['two' => 2, 'other' => 3]);
        $this->assertSame(['one' => ['two' => 2, 'other' => 3]], $conf->getValues());
    }

    public function testSetOverrideByArray()
    {
        $conf = new Configuration(['one' => ['two' => 1]]);
        $conf->set('one', ['other' => 3]);
        $this->assertSame(['one' => ['other' => 3]], $conf->getValues());
    }

    public function testSetPathByDoubleDots()
    {
        $conf = new Configuration(['one' => ['two' => ['three' => 1]]]);
        $conf->set('one:two:three', 3);
        $this->assertSame(['one' => ['two' => ['three' => 3]]], $conf->getValues());
    }

    public function testGet()
    {
        $conf = new Configuration(['one' => ['two' => ['three' => 1]]]);
        $this->assertSame(['one'                 => ['two'   => ['three' => 1]]], $conf->get());
        $this->assertSame(['two'                 => ['three' => 1]], $conf->get('one'));
        $this->assertSame(['three'               => 1], $conf->get('one.two'));
        $this->assertSame(1, $conf->get('one.two.three'));
        $this->assertSame(false, $conf->get('one.two.three.next', false));
    }

    public function testHave()
    {
        $conf = new Configuration(['one' => ['two' => ['three' => 1]]]);
        $this->assertTrue($conf->have('one'));
        $this->assertTrue($conf->have('one.two'));
        $this->assertTrue($conf->have('one.two.three'));
        $this->assertFalse($conf->have('one.two.three.false'));
        $this->assertFalse($conf->have('one.false.three'));
        $this->assertFalse($conf->have('false'));
    }

    public function testSetException()
    {
        $this->setExpectedException('RuntimeException', 'Can not set value at this path (one.two.three.value) because is not array.');

        $conf        = new Configuration();
        $test        = new \stdClass();
        $test->value = 1;

        $conf->set('one.two.three', $test);
        $conf->set('one.two.three.value', 2);
    }

    public function testSetEmpty()
    {
        $conf = new Configuration();
        $conf->set('', ['one' => ['two' => ['three' => 1]]]);
        $this->assertSame(['one' => ['two' => ['three' => 1]]], $conf->get());
    }

    public function testSetValues()
    {
        $conf = new Configuration();
        $conf->setValues(['one' => ['two' => ['three' => 1]]]);
        $this->assertSame(['one'        => ['two'   => ['three' => 1]]], $conf->get());
    }

    public function testAdd()
    {
        $conf = new Configuration();
        $conf->add('bar.baz', ['boo' => true]);
        $this->assertSame(['bar' => ['baz' => ['boo' => true]]], $conf->get());
    }

    public function testArrayMergeRecursiveDistinctInt()
    {
        $conf = new Configuration();
        $conf->add('bar.1', ['0' => true]);
        $conf->add('bar.1', ['test' => false]);
        $conf->add('bar', [1 => ['test' => [false]]]);

        $expected = ['bar' => [1 => [0 => true, 'test' => false], 2 => [0 => true, 'test' => [false]]]];

        $this->assertSame($expected, $conf->get());
    }

    public function testArrayMergeRecursiveDistinctKey()
    {
        $conf = new Configuration();
        $conf->add('bar.foo', ['baz' => true]);
        $conf->add('bar.foo', ['test' => false]);
        $conf->add('bar', ['foo' => ['test' => [false]]]);

        $expected = ['bar' => ['foo' => ['baz' => true, 'test' => [false]]]];

        $this->assertSame($expected, $conf->get());
    }

    public function testArrayMergeRecursiveDistinctElse()
    {
        $conf = new Configuration();
        $conf->add('bar.1', ['0' => true]);
        $this->assertSame(['bar' => [1 => [0 => true]]], $conf->get());
    }

    public function testLoadFileNotExists()
    {
        $path   = './tests/stubs/test-cssprites-not-exists-config.json';
        $conf   = new Configuration();
        $return = $conf->load($path);

        $this->assertSame(false, $return);
    }

    public function testLoadFileEmpty()
    {
        $path   = './tests/stubs/test-cssprites-empty-config.json';
        $conf   = new Configuration();
        $return = $conf->load($path);

        $this->assertSame(false, $return);
    }

    public function testLoadInvalidJson()
    {
        $path   = './tests/stubs/test-cssprites-incorrect-config.json';
        $conf   = new Configuration();
        $return = $conf->load($path);

        $this->assertSame(false, $return);
    }

    public function testLoad()
    {
        $path   = './tests/stubs/test-cssprites-correct-config.json';
        $conf   = new Configuration();
        $return = $conf->load($path);

        $this->assertSame('gd', $conf->get('image.processor.driver'));
        $this->assertSame(4, $conf->get('sprite.spaces'));
        $this->assertSame(true, $return);
    }

    public function testSave()
    {
        $pathL = './tests/stubs/test-cssprites-correct-config.json';
        $pathS = './tests/stubs/test-cssprites-test-save-config.json';

        $conf = new Configuration();
        $conf->load($pathL);
        $return = $conf->save($pathS);

        $this->assertSame(true, $return);
        $this->assertFileEquals($pathS, $pathL);
        unlink($pathS);
    }
}
