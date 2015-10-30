<?php

namespace CSSPrites\Generator;

class HTMLGenerator extends AbstractGenerator
{
    protected $cssGenerator;

    protected $tag      = '';
    protected $template = '';

    protected $cssgenerator;

    public function setTag($value)
    {
        $this->tag = $value;

        return $this;
    }

    public function setTemplate($value)
    {
        $this->template = $value;

        return $this;
    }

    public function setCSSGenerator(CSSGenerator $cssGenerator)
    {
        $this->cssgenerator = $cssGenerator;

        return $this;
    }

    public function addLine($filename)
    {
        $this->content .= "\t".'<'.$this->tag.' class="{{selector}} {{prefix}}-'.$filename.'"></'.$this->tag.'>'."\n";

        return $this;
    }

    public function process()
    {
        if (!($this->cssgenerator instanceof CSSGenerator)) {
            throw new \Exception('CSSGenerator not set');
        }

        $this->content = str_replace(
            ['{{selector}}', '{{prefix}}'],
            [$this->cssgenerator->getSelector(), $this->cssgenerator->getPrefix()],
            $this->content
        );
        $this->content = str_replace(
            ['{{stylesheet}}', '{{content}}'],
            [pathinfo($this->cssgenerator->getFilePath(), PATHINFO_BASENAME), rtrim($this->content)],
            $this->template
        );

        return $this->content;
    }

    public function save()
    {
        if (!($this->cssgenerator instanceof CSSGenerator)) {
            throw new \Exception('CSSGenerator not set');
        }

        return parent::save();
    }
}
