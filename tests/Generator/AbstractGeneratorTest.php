<?php

use CSSPrites\Generator\CSSGenerator;

class AbstractGeneratorTest extends AbstractBaseTest
{
    public function testOverwrite()
    {
        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->overwrite(true);

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
    }

    public function testClear()
    {
        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->clear();

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
        $this->assertSame('', $cssgenerator->process());
    }

    public function testConfigure()
    {
        $config = [
            'imagename' => 'test-output.png',
            'selector'  => 'testSelector',
            'prefix'    => 'testPrefix',
            'filename'  => 'output.ext',
        ];

        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->configure($config);

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
        $this->assertSame($cssgenerator->getSelector(), $config['selector']);
        $this->assertSame($cssgenerator->getPrefix(), $config['prefix']);
        $this->assertSame($cssgenerator->getFilePath(), './'.$config['filename']);
    }

    public function testConfigureException()
    {
        $this->setExpectedException('Exception', 'Undefined configuration property "unexistingkey"');

        $config = [
            'unexistingkey' => 'value',
        ];

        $cssgenerator = new CSSGenerator();
        $cssgenerator->configure($config);
    }

    public function testFilePath()
    {
        $path = '/home/test.css';

        $cssgenerator = new CSSGenerator();
        $return       = $cssgenerator->setFilepath($path);

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $return);
        $this->assertSame($cssgenerator->getFilePath(), $path);
    }

    public function testSaveExceptionFilePath()
    {
        $this->setExpectedException('Exception', 'No file path set on the Generator');

        $cssgenerator = new CSSGenerator();
        $cssgenerator->save();
    }

    public function testSaveExceptionOverwrite()
    {
        $this->setExpectedException('Exception', 'File "./tests/stubs/test-abstract-generator-overwrite.css" already exists and overwriting is disabled');

        $cssgenerator = new CSSGenerator();
        $cssgenerator->setFilepath('./tests/stubs/test-abstract-generator-overwrite.css');
        $cssgenerator->overwrite(false);
        $cssgenerator->save();
    }

    public function testSave()
    {
        $path = './tests/stubs/test-abstract-generator-output.css';

        $cssgenerator = new CSSGenerator();
        $cssgenerator->setFilepath($path);
        $cssgenerator->overwrite(true);
        $cssgenerator->save();

        $this->assertInstanceOf('CSSPrites\Generator\CSSGenerator', $cssgenerator);
        $this->assertFileExists($path);
        $this->assertFileEquals('./tests/stubs/test-abstract-generator-output-expected.css', $path);
        unlink($path);
    }
}
