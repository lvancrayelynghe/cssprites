<?php

namespace CSSPrites;

/**
 * Configuration with dot notation for access multidimensional arrays.
 *
 * $config = new Configuration(['bar'=>['baz'=>['foo'=>true]]]);
 *
 * $value = $config->get('bar.baz.foo'); // $value == true
 *
 * $config->set('bar.baz.foo', false); // ['foo'=>false]
 *
 * $config->add('bar.baz', ['boo'=>true]); // ['foo'=>false,'boo'=>true]
 *
 * Source : https://gist.github.com/elfet/4713488
 *
 * @author Anton Medvedev <anton (at) elfet (dot) ru>
 * @author Luc Vancrayelynghe
 *
 * @version 1.1
 *
 * @license MIT
 */
class Configuration
{
    const SEPARATOR = '/[:\.]/';

    /**
     * @type array
     */
    protected $values = [];

    /**
     * Create a new configuration and set the default values.
     *
     * @param mixed $value The values
     */
    public function __construct(array $values = [])
    {
        $this->values = $values;
    }

    /**
     * Get a value by its "dot-noted" path.
     *
     * @param string $path    The dot notation path (or empty for all values)
     * @param mixed  $default A default value
     *
     * @return mixed The value if found, else default
     */
    public function get($path = '', $default = null)
    {
        if (empty($path)) {
            return $this->values;
        }

        $array = $this->values;
        $keys  = $this->explode($path);
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return $default;
            }
            $array = $array[$key];
        }

        return $array;
    }

    /**
     * Set a value by its "dot-noted" path.
     *
     * @param string $path  The dot notation path (or empty for all values)
     * @param mixed  $value The value(s)
     *
     * @return bool Success
     */
    public function set($path = '', $value)
    {
        if (empty($path)) {
            $this->values = $value;

            return true;
        }

        $at   = &$this->values;
        $keys = $this->explode($path);

        while (count($keys) > 0) {
            if (count($keys) === 1) {
                if (!is_array($at)) {
                    throw new \RuntimeException("Can not set value at this path ($path) because is not array.");
                }
                $at[array_shift($keys)] = $value;
            } else {
                $key = array_shift($keys);

                if (!isset($at[$key])) {
                    $at[$key] = [];
                }

                $at = &$at[$key];
            }
        }

        return true;
    }

    /**
     * Add new configurations to existing conf.
     *
     * @param string $path
     * @param array  $values
     *
     * @return bool
     */
    public function add($path, array $values)
    {
        $get = (array) $this->get($path);

        return $this->set($path, $this->arrayMergeRecursiveDistinct($get, $values));
    }

    /**
     * Check if a configuration exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function have($path)
    {
        $keys  = $this->explode($path);
        $array = $this->values;
        foreach ($keys as $key) {
            if (!isset($array[$key])) {
                return false;
            }
            $array = $array[$key];
        }

        return true;
    }

    /**
     * Set all the configs.
     *
     * @param array $values
     *
     * @return bool
     */
    public function setValues($values)
    {
        $this->values = $values;

        return true;
    }

    /**
     * Get all the configs.
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * [explode description].
     *
     * @param string $path [description]
     *
     * @return array
     */
    protected function explode($path)
    {
        // Faster than explode() ?
        return preg_split(self::SEPARATOR, $path);
    }

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):.
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * arrayMergeRecursiveDistinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * If key is integer, it will be merged like array_merge do:
     * arrayMergeRecursiveDistinct(array(0 => 'org value'), array(0 => 'new value'));
     *     => array(0 => 'org value', 1 => 'new value');
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     *
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     * @author Anton Medvedev <anton (at) elfet (dot) ru>
     */
    protected function arrayMergeRecursiveDistinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                if (is_int($key)) {
                    $merged[] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
                } else {
                    $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
                }
            } else {
                if (is_int($key)) {
                    $merged[] = $value;
                } else {
                    $merged[$key] = $value;
                }
            }
        }

        return $merged;
    }

    /**
     * Loads config from a JSON file.
     *
     * @param string $filepath Path to the file where to read the config
     *
     * @return bool
     */
    public function load($filepath)
    {
        if (!file_exists($filepath)) {
            return false;
        }

        $datas = file_get_contents($filepath);
        if (!$datas) {
            return false;
        }

        $datas = json_decode($datas, true);
        if (!$datas) {
            return false;
        }

        $this->setValues($datas);

        return true;
    }

    /**
     * Saves config to a JSON file.
     *
     * @param string $filepath Path to the file where to write the config
     *
     * @return bool
     */
    public function save($filepath)
    {
        return file_put_contents($filepath, json_encode($this->getValues(), JSON_PRETTY_PRINT)) > 0;
    }
}
