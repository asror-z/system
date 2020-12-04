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


use Cocur\Slugify\Slugify;
use yii\helpers\StringHelper;

use zetsoft\models\ALL;
use zetsoft\system\Az;

class ZStringHelper extends StringHelper
{

    public static function catModel($class)
    {
        $className = bname($class);
        $all = ALL::run();
        foreach ($all as $name => $title) {
            if (ZStringHelper::startsWith($className, $name, false))
                return $name;
        }

        return null;
    }

    public static function catForm($class)
    {
        $className = bname($class);

        $all = \zetsoft\former\ALL::run();

        foreach ($all as $name) {
            if (ZStringHelper::startsWith($className, $name, false))
                return $name;
        }

        return null;
    }


    public static function isApp($class)
    {
        $className = bname($class);
        $all = ALL::run();

        foreach ($all as $name) {
            if (ZStringHelper::startsWith($className, lcfirst($name), false))
                return true;
        }


        /*   if (ZStringHelper::startsWith($class, App, false))
               return true;*/

        return false;
    }

    public static function full($class)
    {
        $className = bname($class);
        $app = self::catModel($className);
        return "zetsoft\models\\{$app}\\{$className}";
    }


    public static function fullForm($class)
    {

        $className = bname($class);
        $app = substr($className,0,preg_match_all('/[A-Z]/', $className, $matches, PREG_OFFSET_CAPTURE)+1);

        return "zetsoft\\former\\{$app}\\{$className}";
    }

    public static function path2class($file)
    {
        /**
         *
         * Class Forming
         */
        $pathRoot = ZFileHelper::normalizePath(Root);

        $control = str_replace([$pathRoot, '.php'], '', $file);
        $control = ZFileHelper::normLinux("zetsoft/$control");

        Az::debug($control, 'Processing CoreControl');
    }

    /**
     * Find the position of the first occurrence of a substring in a string
     * @param string $haystack <p>
     * The string to search in
     * </p>
     * @param mixed $needle <p>
     * If <b>needle</b> is not a string, it is converted
     * to an integer and applied as the ordinal value of a character.
     * </p>
     * @param int $offset [optional] <p>
     * If specified, search will start this number of characters counted from
     * the beginning of the string. Unlike {@see strrpos()} and {@see strripos()}, the offset cannot be negative.
     * </p>
     * @return int|false <p>
     * Returns the position where the needle exists relative to the beginnning of
     * the <b>haystack</b> string (independent of search direction
     * or offset).
     * Also note that string positions start at 0, and not 1.
     * </p>
     * <p>
     * Returns <b>FALSE</b> if the needle was not found.
     * </p>
     */
    public static function find($haystack, $needle, $isCaseInSensitive = true): bool
    {
        if ($isCaseInSensitive)
            $pos = stripos($haystack, $needle);
        else
            $pos = strpos($haystack, $needle);

        if ($pos === false)
            return false;

        return true;
    }

    public static function slug($text)
    {

        $isLoverCase = false;
        $delimiter = '';


        $slug = new Slugify([
            'lowercase' => true,
            'separator' => '-',
            'lowercase_after_regexp' => false,
            'trim' => true,
            'strip_tags' => false,
        ]);

        $slug->slugify($text, $delimiter);
    }

    public static function checkName($string, $find)
    {
        $len = strlen($string);
        $pos = stripos($string, $find);

        if ($pos === false)
            return false;

        $end = $len - $pos === strlen($find);
        $begin = $len - $pos === $len;

        return $end || $begin;

    }

    public static function cleanName($string)
    {
        $pattern = '/((.?+)_.+)/i';
        $replacement = '$2';

        return preg_replace($pattern, $replacement, $string);
    }

}
