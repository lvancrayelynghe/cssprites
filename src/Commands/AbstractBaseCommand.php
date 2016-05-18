<?php

namespace CSSPrites\Commands;

use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Symfony\Component\Console\Command\Command;

abstract class AbstractBaseCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $input;
    protected $output;
    protected $dialog;

    /**
     * @param string $option
     *
     * @return string
     */
    protected function getOptionOrAsk($option, $question, $default = null)
    {
        $value = $this->input->getOption($option);
        if (is_null($value)) {
            $this->output->writeln('');
            $value = $this->dialog->ask($this->output, '<question>'.$question.' ?</question> ', $default);
            $this->output->writeln('<comment>'.$question.' is now "'.$value.'"</comment>');
        }

        return $value;
    }

    /**
     * @param string $option
     */
    protected function getOptionOrSelect($option, $question, array $choices, $default = null)
    {
        $value = $this->input->getOption($option);
        if (is_null($value)) {
            $this->output->writeln('');
            $value = $this->dialog->select($this->output, '<question>'.$question.' ?</question> ', $choices, $default);
            $value = $choices[$value];
            $this->output->writeln('<comment>'.$question.' is now "'.$value.'"</comment>');
        }

        if (!in_array($value, $choices)) {
            throw new \Exception('Invalid '.$question.' '.$value.'.');
        }

        return $value;
    }

    /**
     * @param string $option
     */
    protected function getOptionOrAskConfirmation($option, $question, $default = null)
    {
        $value = $this->input->getOption($option);
        if (is_null($value)) {
            $this->output->writeln('');
            $value = $this->dialog->askConfirmation($this->output, '<question>'.$question.' ?</question> ', $default);
            $this->output->writeln('<comment>'.$question.' is now "'.($value ? 'enabled' : 'disabled').'"</comment>');

            return $value;
        }

        $value = ($value === 'true' || $value === 'yes' || $value === 'y' || $value === '1');

        return $value;
    }

    /**
     * @param string $option
     *
     * @return string
     */
    protected function getOptionOrAskAndValidate($option, $question, \Closure $callback, $default = null)
    {
        $value = $this->input->getOption($option);
        if (is_null($value)) {
            $this->output->writeln('');
            $value = $this->dialog->askAndValidate(
                $this->output,
                '<question>'.$question.' ?</question> ',
                $callback,
                $default
            );
            $this->output->writeln('<comment>'.$question.' is now "'.$value.'"</comment>');
        }

        return $value;
    }
}
