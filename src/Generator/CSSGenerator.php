<?php

namespace CSSPrites\Generator;

class CSSGenerator extends AbstractGenerator
{
    protected $imagename = '';

    protected $selector = '';
    protected $prefix   = '';

    protected $mainLine   = '';
    protected $spriteLine = '';

    public function setImage($imagename)
    {
        $this->imagename = $imagename;

        return $this;
    }

    public function setSelector($value)
    {
        $this->selector = $value;

        return $this;
    }

    public function setPrefix($value)
    {
        $this->prefix = $value;

        return $this;
    }

    public function setMainLine($value)
    {
        $this->mainLine = $value;

        return $this;
    }

    public function setSpriteLine($value)
    {
        $this->spriteLine = $value;

        return $this;
    }

    public function getSelector()
    {
        return $this->selector;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function addLine($filename, $x, $y, $w, $h)
    {
        $this->content .= str_replace(
            ['{{filename}}', '{{x}}', '{{y}}', '{{w}}', '{{h}}'],
            [$filename, $x, $y, $w, $h],
            $this->spriteLine
        );

        return $this;
    }

    public function process()
    {
        $this->content = str_replace(
            ['{{image}}', '{{selector}}', '{{prefix}}'],
            [$this->imagename, $this->selector, $this->prefix],
            $this->mainLine.$this->content
        );

        return $this->content;
    }
}
