<?php

namespace CSSPrites;

use CSSPrites\ImageProcessor\ImageProcessorInterface;
use League\Container\ServiceProvider as BaseServiceProvider;

/**
 * ServiceProvider to register all the needed dependencies.
 *
 * @codeCoverageIgnore
 */
class ServiceProvider extends BaseServiceProvider
{
    protected $provides = [
        'configuration',
        'image.processor',
        'sprite.processor.horizontal',
        'sprite.processor.vertical',
        'sprite.processor.simplebinpacking',
        'commands.generate',
    ];

    public function register()
    {
        $container = $this->getContainer();

        // AutoConfigure ContainerAwareInterface
        $container->inflector('League\Container\ContainerAwareInterface')
            ->invokeMethod('setContainer', [$container]);

        // Registering Configuration as singleton
        $container->singleton('configuration', 'CSSPrites\Configuration');

        // Registering Image Processor
        $container->add('image.processor', 'CSSPrites\ImageProcessor\ImagineImageProcessor');

        // AutoConfigure Image Processor
        $container->inflector('CSSPrites\ImageProcessor\ImageProcessorInterface', function (ImageProcessorInterface $instance) use ($container) {
            $instance->setConfig($container->get('configuration')->get('image.processor'));
        });

        // Registering Sprites Processors
        $container->add('sprite.processor.horizontal', 'CSSPrites\SpriteProcessor\HorizontalSpriteProcessor')
             ->withArgument('image.processor');
        $container->add('sprite.processor.vertical', 'CSSPrites\SpriteProcessor\VerticalSpriteProcessor')
             ->withArgument('image.processor');
        $container->add('sprite.processor.simplebinpacking', 'CSSPrites\SpriteProcessor\SimpleBinPackingSpriteProcessor')
             ->withArgument('image.processor');

        // Registering ConsoleCommands
        $container->add('commands.generate', 'CSSPrites\Commands\GenerateCommand');
    }
}
