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


use yii\helpers\Inflector;
use zetsoft\system\Az;

class ZInflector extends Inflector
{
    /**
     * Converts any "CamelCased" into an "underscored-word".
     * @param string $words the word(s) to underscore
     * @return string
     */
    public static function dash($words)
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '-\\1', $words));
    }

    public static function classClean($model)
    {

        $model = ZFileHelper::normalizePath($model, DS);
        $model = str_replace('.php', '', $model);

        return $model;
    }


    public static function classSpace($model)
    {

        $class = strtr($model, [
            Az::getAlias('@zetsoft') => 'zetsoft',
            '/' => '\\'
        ]);

        return $class;
    }


    public static function classAll($model, $basename = false)
    {

        $model = static::classClean($model);
        $model = static::classSpace($model);

        if ($basename)
            $model = bname($model);

        return $model;
    }


    public static function actionize($string)
    {

        $return = self::underscore($string);
        $return = str_replace('_', '-', $return);

        return $return;
    }

    /**
     *
     * Function  startWithNumber
     * @param $string
     * @return  bool
     * @author Daho
     * @since 17.08.2020
     */
    public static function startWithNumber($string){
        return is_numeric($string[0]);
    }

    /**
     *
     * Function  isCrylic
     * @param $string
     * @return  bool
     * @author Daho
     * @since 17.08.2020
     */
    public static function isCrylic($string){
          return (bool)preg_match('/^[А-Яа-я\s_-]+$/u', $string);
    }

    /**
     *
     * Function  isLatin
     * @param $string
     * @return  bool
     * @author Daho
     * @since 17.08.2020
     */
    public static function isLatin($string){
        return (bool)preg_match('/^[A-Za-z\s_-]+$/u', $string);
    }


}
