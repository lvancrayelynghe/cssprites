<?php

namespace CSSPrites;

abstract class AbstractConfigurable
{
    public function configure(array $config = [])
    {
        if (is_array($config) && array_key_exists('filename', $config)) {
            $config['filepath']  = isset($config['filepath']) && is_string($config['filepath']) ? $config['filepath'] : '.';
            $config['filepath'] .= '/';
            $config['filepath'] .= isset($config['filename']) && is_string($config['filename']) ? $config['filename'] : 'sprite.png';
            unset($config['filename']);
        }

        foreach ($config as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \Exception('Undefined configuration property "'.$key.'"');
            }
            $this->$key = $value;
        }

        return $this;
    }
}
