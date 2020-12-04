<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\helpers;


use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;
use zetsoft\system\Az;

class ZArrayHelper extends ArrayHelper
{


    /**
     * @param mixed $needle The value to look for.
     * @param array|\Traversable $haystack The set of values to search.
     * @param bool $strict Whether to enable strict (`===`) comparison.
     * @return bool `true` if `$needle` was found in `$haystack`, `false` otherwise.
     * @throws InvalidArgumentException if `$haystack` is neither traversable nor an array.
     **/
    public static function isIn($needle, $haystack, $strict = true)
    {
        return parent::isIn($needle, $haystack, $strict);
    }

    /**
     * @param string $key the key to check
     * @param array $array the array with keys to check
     * @param bool $caseSensitive whether the key comparison should be case-sensitive
     * @return bool whether the array contains the specified key
     **/
    public static function keyExists($key, $array, $caseSensitive = true)
    {

        $debug = false;

        if ($array === null)
            if ($debug)
                vdd('Array is Null', $array, trace());
            else
                return null;

        if (is_object($array))
            if ($debug)
                vdd('Array is object', $array, trace());
            else
                return null;

        if (is_string($array))
            if ($debug)
                vdd('Array is string', $array, trace());
            else
                return null;

        if (is_int($array))
            if ($debug)
                vdd('Array is INT', $array, trace());
            else
                return null;

        if (!is_array($array))
            if ($debug)
                vdd('Var is Not Array', $array, trace());
            else
                return null;

        return parent::keyExists($key, $array, $caseSensitive);
        //   return self::keyCheck($key, $array, $caseSensitive);
    }

    public static function keyCheck($key, $array, $caseSensitive = true)
    {
        return (isset($array[$key]) || parent::keyExists($key, $array, $caseSensitive));
    }

    public static function keyExistsNorm($key, $array, $caseSensitive = false)
    {
        if (self::keyExists($key, $array))
            if (!empty($array[$key]))
                return true;

        return false;

    }

    /**
     * To get array levels count
     * Function  arrayLevels
     * @param array $array
     * @return  int
     * @author Daho
     * @since 07.08.2020
     */
    public static function levels($array)
    {
        $level = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = ZArrayHelper::levels($value) + 1;

                if ($depth > $level) {
                    $level = $depth;
                }
            }
        }

        return $level;
    }

    public static function createArray($array = [], $key1, $key2, $index1 = 'id', $index2 = 'text')
    {
        $return = [];
        foreach ($array as $key => $item) {
            $return[$key][$index1] = $item[$key1];
            $return[$key][$index2] = $item[$key2];
        }
        return $return;
    }

}
