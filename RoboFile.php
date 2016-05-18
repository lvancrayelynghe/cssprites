<?php

use Symfony\Component\Finder\Finder;

class RoboFile extends \Robo\Tasks
{
    /**
     * Build the PHAR file.
     */
    public function buildPhar()
    {
        if (!file_exists('build/phar')) {
            mkdir('build/phar', 0755, true);
        }

        if (!function_exists('token_get_all')) {
            $this->say('<error>Function "token_get_all()" is not available. To create a smaller phar file, please install ....</error>');
        }

        $this->say('Preparing phar creation');
        $packer = $this->taskPackPhar('build/phar/cssprites.phar');
        $packer->compress();

        $this->say('Adding files');
        $finder = Finder::create()
            ->ignoreVCS(true)
            ->name('*.php')
            ->path('src')
            ->path('vendor')
            ->notPath('vendor/symfony/finder/Tests')
            ->notPath('vendor/symfony/console/Tests')
            ->notPath('vendor/symfony/jbroadway/urlify/tests')
            ->notPath('vendor/symfony/jbroadway/urlify/scripts')
            ->in(__DIR__);
        foreach ($finder as $file) {
            $packer->addStripped($file->getRelativePathname(), $file->getRealPath());
        }

        $packer->addFile('config/cssprites.json', 'config/cssprites.json');

        $packer->addFile('bin/cssprites', 'bin/cssprites')->executable('bin/cssprites');

        $this->say('Build phar');
        $packer->run();

        $this->say('Test build');
        $this->taskExec('php build/phar/cssprites.phar -V')->run();
    }
}
