<?php

use CSSPrites\Slugifier\SlugifySlugifier;

abstract class AbstractBaseTest extends PHPUnit_Framework_TestCase
{
    protected $slugifier;

    public function setUp()
    {
        $this->slugifier = new SlugifySlugifier();
    }

    /**
     * Asset that 2 images are equals.
     *
     * Requires imagemagick installed
     * sudo apt-get install imagemagick
     *
     * @param string $expected Expected filepath
     * @param string $actual   Actual filepath
     * @param string $message  Error message
     */
    protected function assertImageEquals($expected, $actual, $message = '')
    {
        exec('which compare', $output);
        if (empty($output)) {
            $this->fail('This test require imagemagick command line tool "compare"');

            return;
        }

        if (!file_exists($expected)) {
            $this->fail('File "'.$expected.'" does not exist');
        }
        if (!file_exists($actual)) {
            $this->fail('File "'.$actual.'" does not exist');
        }

        $descriptors = array(
            array('pipe', 'r'),
            array('pipe', 'w'),
            array('pipe', 'w'),
        );
        $command = 'compare -metric RMSE '.escapeshellarg($expected).' '.escapeshellarg($actual).' /dev/null';
        $proc    = proc_open($command, $descriptors, $pipes);

        $diff = stream_get_contents($pipes[2]);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        preg_match('#\((.+)\)#', $diff, $match);
        if (empty($match) || !isset($match[1])) {
            $this->fail($diff);

            return;
        }

        $threshold = floatval($match[1]);
        $this->assertLessThan(0.05, $threshold, $message);
    }
}
