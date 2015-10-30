<?php

$iterator = Symfony\Component\Finder\Finder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__.'/src')
;

return new Sami\Sami($iterator, array(
    'title'                => 'CSSPrites PHP API',
    'build_dir'            => __DIR__.'/build/doc',
    'cache_dir'            => __DIR__.'/build/doc/cache',
    'default_opened_level' => 2,
));
