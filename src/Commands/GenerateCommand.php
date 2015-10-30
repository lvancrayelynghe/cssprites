<?php

namespace CSSPrites\Commands;

use CSSPrites\Generator\CSSGenerator;
use CSSPrites\Generator\HTMLGenerator;
use CSSPrites\ImagesCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends AbstractBaseCommand
{
    protected $startTime;

    protected $input;
    protected $output;

    protected function getOptions()
    {
        $options = [
            'input' => [
                'shortcut'    => 'i',
                'description' => 'Directory to parse',
                'default'     => getcwd(),
            ],
            'output' => [
                'shortcut'    => 'o',
                'description' => 'Output directory',
                'default'     => getcwd(),
            ],
            'mask' => [
                'shortcut'    => 'm',
                'description' => 'File mask',
                'default'     => '*.png',
            ],
            'overwrite' => [
                'shortcut'    => 'O',
                'description' => 'Overwrite files (sprite image, css & html) if they exists',
                'default'     => true,
            ],
            'driver' => [
                'shortcut'    => 'd',
                'description' => 'Image driver (gd, imagick or gmagick)',
                'default'     => 0,
                'choices'     => ['gd', 'imagick', 'gmagick'],
            ],
            'spaces' => [
                'shortcut'    => 's',
                'description' => 'Spaces between images in the sprite',
                'default'     => 4,
            ],
            'sprite' => [
                'shortcut'    => 'S',
                'description' => 'Final sprite image file name',
                'default'     => 'sprite.png',
            ],
            'background' => [
                'shortcut'    => 'b',
                'description' => 'Sprite background color (hex color, null for transparency)',
                'default'     => null,
            ],
            'selector' => [
                'shortcut'    => 'c',
                'description' => 'CSS class selector',
                'default'     => 'sprite',
            ],
        ];

        foreach ($options as $name => $option) {
            $question = $option['description'];

            if (array_key_exists('default', $option)) {
                if (is_string($option['default'])) {
                    $question .= ' (default to "'.$option['default'].'")';
                } elseif (is_int($option['default'])) {
                    $question .= ' (default to '.$option['default'].')';
                } elseif (is_bool($option['default'])) {
                    $question .= ' (default to '.($option['default'] ? 'true' : 'false').')';
                } elseif (is_null($option['default'])) {
                    $question .= ' (default to null)';
                }
            }

            $options[$name]['question'] = $question;
        }

        return $options;
    }

    protected function configure()
    {
        $this->setName('generate')->setDescription('Create a new sprite');

        foreach ($this->getOptions() as $name => $option) {
            $this->addOption($name, $option['shortcut'], InputOption::VALUE_REQUIRED, $option['question']);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->startTime = microtime(true);
        $this->dialog    = $this->getHelperSet()->get('dialog');
        $this->input     = $input;
        $this->output    = $output;

        $this->populateConfiguration();

        $config = $this->getContainer()->get('configuration');

        $css  = new CSSGenerator();
        $css->configure($config->get('css'));
        $css->setImage($config->get('sprite.filename'));

        $html  = new HTMLGenerator();
        $html->configure($config->get('html'));
        $html->setCSSGenerator($css);

        $collection = new ImagesCollection(
            $this->getContainer()->get('image.processor'),
            $config->get('input.directory'),
            $config->get('input.mask'),
            $config->get('sprite.filename')
        );

        $this->output->writeln('<comment>Generating Sprite...</comment>');
        $sprite = $this->getContainer()->get('sprite.processor.'.$config->get('sprite.processor', 'simplebinpacking'))
            ->configure($config->get('sprite'))
            ->setImages($collection)
            ->process();

        foreach ($collection->get() as $image) {
            $css->addLine(
                $image->getSimpleName(),
                -$image->getX(),
                -$image->getY(),
                $image->getWidth(),
                $image->getHeight()
            );
            $html->addLine($image->getSimpleName());
        }

        $this->output->writeln('<comment>Saving Sprite...</comment>');
        $sprite->save();

        $this->output->writeln('<comment>Saving CSS...</comment>');
        $css->save();

        $this->output->writeln('<comment>Saving HTML...</comment>');
        $html->save();

        $totalTime = microtime(true) - $this->startTime;
        $this->output->writeln('<info>Done in '.number_format($totalTime, 4, '.', '').' µs</info>');
    }

    protected function populateConfiguration()
    {
        $options = $this->getOptions();
        $config  = $this->getContainer()->get('configuration');

        // Input Directory
        $config->set('input.directory', $this->getInputDirectory($options['input']));
        if ($config->load($config->get('input.directory').'/cssprites.json')) {
            $this->output->writeln('<info>Config file "cssprites.json" found and loaded</info>');
            $this->output->writeln('');

            return true;
        }

        // Output Directory
        $options['output']['question'] = str_replace(getcwd(), $config->get('input.directory'), $options['output']['question']);
        $config->set('sprite.filepath', $this->getOutputDirectory($options['output'], $config->get('input.directory')));
        $config->set('css.filepath', $config->get('sprite.filepath'));
        $config->set('html.filepath', $config->get('sprite.filepath'));

        // File mask
        $config->set('input.mask', $this->getFileMask($options['mask']));

        // Get image driver
        $config->set('image.processor.driver', $this->getDriver($options['driver']));

        // Output filename
        $filename = $this->getOutputFilename($options['sprite']);
        $config->set('sprite.filename', $filename);
        $config->set('css.filename', substr($filename, 0, strrpos($filename, '.') + 1).'css');
        $config->set('html.filename', substr($filename, 0, strrpos($filename, '.') + 1).'html');

        // Overwrite
        $config->set('sprite.overwrite', $this->getOverwrite($options['overwrite']));
        $config->set('css.overwrite', $config->get('sprite.overwrite'));
        $config->set('html.overwrite', $config->get('sprite.overwrite'));

        // Sprite spaces
        $config->set('sprite.spaces', $this->getSpaces($options['spaces']));

        // Background
        $config->set('sprite.background', $this->getBackground());

        // CSS Selector
        $selector = $this->getCssSelector($options['selector']);
        $config->set('css.selector', $selector);
        $config->set('css.prefix', $selector);

        // Save config file
        $this->output->writeln('');
        if ($this->getSaveConfiguration()) {
            $config->save($config->get('input.directory').'/cssprites.json');
        }

        $this->output->writeln('');

        return true;
    }

    protected function getInputDirectory($option)
    {
        $directory = $this->getOptionOrAskAndValidate(
            'input',
            $option['question'],
            function ($directory) {
                $directory = is_null($directory) ? getcwd() : $directory;

                return realpath($directory);
            },
            $option['default']
        );

        $directory = realpath($directory);
        if ($directory === false) {
            throw new \Exception('Directory '.$directory.' doesn\'t exists.');
        }

        return $directory;
    }

    protected function getOutputDirectory($option, $default)
    {
        $directory = $this->getOptionOrAskAndValidate(
            'output',
            $option['question'],
            function ($directory) use ($default) {
                $directory = is_null($directory) ? $default : $directory;

                return $directory;
            },
            $default
        );

        $directory = realpath($directory);
        if ($directory === false) {
            throw new \Exception('Directory '.(string) $directory.' doesn\'t exists.');
        }

        return $directory;
    }

    protected function getFileMask($option)
    {
        return $this->getOptionOrAsk('mask', $option['question'], $option['default']);
    }

    protected function getDriver($option)
    {
        return $this->getOptionOrSelect('driver', $option['question'], $option['choices'], $option['default']);
    }

    protected function getOutputFilename($option)
    {
        $filename = $this->getOptionOrAsk('sprite', $option['question'], $option['default']);
        $filename = strrpos($filename, '.') === false ? $filename.'.png' : $filename;

        return $filename;
    }

    protected function getOverwrite($option)
    {
        return $this->getOptionOrAskConfirmation('overwrite', $option['question'], $option['default']);
    }

    protected function getSpaces($option)
    {
        $spaces = $this->getOptionOrAskAndValidate(
            'spaces',
            $option['question'],
            function ($spaces) {
                $spaces = is_null($spaces) ? 4 : $spaces;
                $spaces = (int) $spaces;
                if ($spaces < 0) {
                    throw new \Exception('Invalid spaces (number >= 0)');
                }

                return $spaces;
            },
            $option['default']
        );

        return $spaces;
    }

    protected function getBackground()
    {
        $background = $this->input->getOption('background');
        $background = $background === 'null' ? null : $background;

        return $background;
    }

    protected function getCssSelector($option)
    {
        return $this->getOptionOrAsk('selector', $option['question'], $option['default']);
    }

    protected function getSaveConfiguration()
    {
        return $this->dialog->askConfirmation(
            $this->output,
            '<question>Save configuration to "cssprites.json" ? (y/n, default to n)</question> ',
            false
        );
    }
}
