<?php

use Symfony\CS\Config\Config;
use Symfony\CS\Finder\DefaultFinder;
use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;
use Symfony\CS\FixerInterface;

HeaderCommentFixer::setHeader('');

$level = FixerInterface::SYMFONY_LEVEL;

$additionnalFixers = [
    '-unalign_equals',
    '-unalign_double_arrow',
    'align_double_arrow',
    'align_equals',
    'ordered_use',
    'php_unit_strict',
    'phpdoc_order',
    'phpdoc_var_to_type',
];

$finder = DefaultFinder::create()
    ->exclude('build')
    ->exclude('vendor')
    ->exclude('config')
    ->in(__DIR__);

$config = Config::create()
    ->setUsingCache(true)
    ->level($level)
    ->fixers($additionnalFixers)
    ->finder($finder)
;

return $config;
