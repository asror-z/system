<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    06.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\helpers;


use phpDocumentor\Reflection\Types\Self_;
use yii\helpers\VarDumper;
use zetsoft\system\Az;
use function GuzzleHttp\Psr7\str;

class ZVarDumper extends VarDumper
{

    public static function bool($value)
    {
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            default:
                return $value;
        }

    }


    public static function arrayFilter($array, $bool = false)
    {

        if (empty($array))
            return [];

        $return = [];
        foreach ($array as $key => $value) {
            $return[$key] = self::ajaxValue($value, $bool);
        }

        return $return;


    }


    public static function arrayFilterCheck($array, $bool = false)
    {

        if (empty($array))
            return [];

        $return = [];
        foreach ($array as $key => $value) {
            $return[$key] = self::checkValue($value, $bool);
        }

        return $return;


    }


    public static function arrayFilterAjax($array, $bool = false)
    {

        if (empty($array))
            return [];

        $return = [];
        foreach ($array as $key => $value) {

            if ($value === null)
                $value = 'null';

            $return[$key] = $value;
        }

        return $return;


    }


    public static function cleanEmpty($value)
    {
        if ($value === null) {
            return false;
        }

        $newArray = null;
        foreach ($value as $key => $item) {
            if ($item !== '') {
                $newArray[$key] = $item;
            }
        }
        return $newArray;
    }

    public static function dbTypeCast($var, $type)
    {
        switch ($type) {
            case dbTypeBoolean:
                return (bool)$var;
                break;

            case dbTypeInteger:
            case dbTypeBigInteger:
                return (int)$var;
                break;

            case dbTypeString:
                return json_encode($var);
                break;

            case dbTypeJsonb:
            case dbTypeJson:
//                if ($var === '""')
//                    return [];
//                return json_decode($var, true, 512, JSON_THROW_ON_ERROR);
                break;

            default:
                return $var;
        }
    }


    public static function widget($value)
    {
        $return = null;

        switch (true) {

            case is_bool($value):
                if ($value)
                    $return = true;
                else
                    $return = false;
                break;

            case is_array($value):
                if (empty($value))
                    $return = [];
                else
                    $return = $value;
                break;

            case is_int($value):
                $return = (int)$value;
                break;

            case $value === null:
                $return = null;
                break;

            default:
                $return = $value;

        }

        return $return;

    }


    public static function contentValue($value)
    {

        $return = $value;

        switch ($value) {

            case false:
                $return = false;
                break;

            case true:
                $return = true;
                break;

        }

        return $return;

    }

    public static function ajaxValue($value, $bool = false)
    {
        $return = null;

        switch (true) {

            case is_int($value):
                /*
                            case $value === '0':
                            case $value === '1':
                */
                $return = (int)$value;
                break;


            case $value === 'false':
                $return = false;
                break;


            case $value === 'true':
                $return = true;
                break;


            case $value === '':
                $return = null;
                break;


            default:
                $return = $value;

        }

        if ($bool) {
            if ($value === 'false' || $value === '0' || $value === 'null')
                $return = null;

        }

        return $return;

    }

    public static function checkValue($value, $bool = false)
    {
        $return = null;

        switch (true) {

            case is_int($value):
                $return = (int)$value;
                break;

            case $value === 'false':
                $return = false;
                break;

            case $value === 'true':
                $return = true;
                break;

            case $value === 'null':
            case $value === '':
                $return = null;
                break;

            default:
                $return = $value;

        }

        if ($bool) {
            if ($value === 'false' || $value === '0' || $value === 'null')
                $return = null;

        }

        return $return;

    }

    public static function grapesValue($value, $bool = false)
    {
        $return = null;

        switch (true) {

            case $value === 'false':
                $return = false;
                break;


            case $value === 'true':
                $return = true;
                break;

            case $value === '':
                $return = null;
                break;

            default:
                $return = $value;

        }

        if ($bool) {
            if ($value === 'false' || $value === '0' || $value === 'null')
                $return = null;

        }

        return $return;

    }

    public static function normalizeBool($var)
    {
        if ($var)
            return 'true';

        return 'false';
    }


    public static function jscode($value)
    {
        $return = null;
        switch (gettype($value)) {
            case 'boolean':
                if ($value)
                    $return = true;
                else
                    $return = 0;
                break;

            case 'array':
                $return = ZJsonHelper::encode($value);
                break;

            default:
                $return = $value;


        }

        return $return;
    }


    public static function value($value, $tabCount = 16)
    {
        $return = null;
        switch (true) {
            case is_bool($value):
                if ($value)
                    $return = true;
                else
                    $return = false;
                break;

            case is_array($value):
                $return = ZVarDumper::exportTab($value, $tabCount);
                break;

            case is_string($value):
                $return = "'$value'";
                break;

            default:
                $return = $value;

        }

        return $return;
    }


    public static function exportTab($var, $tabCount = 8)
    {
        $tab = EOL . str_repeat(' ', $tabCount);
        $export = parent::export($var);

        return str_replace("\n", $tab, $export);

    }

    public static function exportSafe($var, $tabCount = 16)
    {

        if (is_array($var) && ZArrayHelper::keyExists('event', $var)) {
            $tab = EOL . str_repeat(' ', 6);
            $tab2 = "\n" . str_repeat(' ', $tabCount + 2);
            $export = parent::export($var);
            $string = str_replace('  ', '', $export);
            $return = str_replace(array("\n", "\r\n", '                        }\''), array($tab2, $tab, '                  }\''), $string);
        } else {
            $tab = EOL . str_repeat(' ', $tabCount);
            $export = parent::export($var);

            $return = str_replace("\n", $tab, $export);
        }
        return $return;
    }


    public static function beauty($value)
    {

        $export = self::export($value);

        $export = strtr($export, [
            "'" => '',
            '[' => '',
            ']' => '',
        ]);

        return $export;
    }

    public static function search($value)
    {
        $export = self::export($value);

        $replaces = [
            "\n" => '',
            "'" => '"',
            ',]' => ']',
            ' ' => '',
        ];

        foreach ($replaces as $replace => $needle) {
            $export = str_replace($replace, $needle, $export);
        }

        $export = sprintf("'%s'", $export);

        return $export;
    }


}
