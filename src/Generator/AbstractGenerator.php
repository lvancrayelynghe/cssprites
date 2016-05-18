<?php

namespace CSSPrites\Generator;

use CSSPrites\AbstractConfigurable;
use CSSPrites\Slugifier\SlugifierInterface;

abstract class AbstractGenerator extends AbstractConfigurable
{
    protected $slugifier;

    protected $filepath;
    protected $overwrite = false;

    protected $content = '';

    public function __construct(SlugifierInterface $slugifier)
    {
        $this->slugifier = $slugifier;
    }

    public function overwrite($value)
    {
        $this->overwrite = $value;

        return $this;
    }

    public function clear()
    {
        $this->content = '';

        return $this;
    }

    public function getFilePath()
    {
        return $this->filepath;
    }

    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;

        return $this;
    }

    public function save()
    {
        if (empty($this->filepath)) {
            throw new \Exception('No file path set on the Generator');
        }

        if ($this->overwrite === false && file_exists($this->filepath)) {
            throw new \Exception('File "'.$this->filepath.'" already exists and overwriting is disabled in the Processor');
        }

        $this->process();

        return file_put_contents($this->filepath, $this->content);
    }
}
