<?php
/**
 *
 * Author:  Asror Zakirov
 * Created: 05.03.2018 10:46
 * https://www.linkedin.com/in/asror-zakirov-167961a9
 */

namespace zetsoft\system;

use Monolog\Logger;
use yii\base\Exception;
use yii\helpers\VarDumper;
use zetsoft\service\ALL\Cores;
use zetsoft\service\cores\Langs;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\module\ZCmdApp;
use zetsoft\system\module\ZWebApp;

/**
 * Class    Az
 * @package zetsoft\system
 *
 * @property ZWebApp|ZCmdApp $app
 */
class Az extends \Yii
{

    #region File
    public static function getAliasNorm($alias)
    {
        $path = Az::getAlias($alias);
        return ZFileHelper::normLinux($path);
    }

    #endregion


    #region Log

    public static function api($data, $title = null, $category = null, int $eol = 0)  //NULL??
    {
        $resultString = static::join($data, $title, $eol);
        //\Yii::error($resultString, $category);
        Az::$app->utility->monolog->log($resultString, Logger::API);
        return false;
    }

    public static function notice($data, $title = null, $category = null, int $eol = 0)  //NULL??
    {
        $resultString = static::join($data, $title, $eol);
        //\Yii::error($resultString, $category);
        Az::$app->utility->monolog->log($resultString, Logger::NOTICE);
        return false;
    }

    public static function emergency($data, $title = null, $category = null, int $eol = 0)  //NULL??
    {
        $resultString = static::join($data, $title, $eol);
        Az::$app->utility->monolog->log($resultString, Logger::EMERGENCY);
        return false;
    }

    public static function alert($data, $title = null, $category = null, int $eol = 0)  //NULL??
    {
        $resultString = static::join($data, $title, $eol);
        Az::$app->utility->monolog->log($resultString, Logger::ALERT);
        return false;
    }

    public static function critical($data, $title = null, $category = null, int $eol = 0)  //NULL??
    {
        $resultString = static::join($data, $title, $eol);
        //\Yii::error($resultString, $category);
        Az::$app->utility->monolog->log($resultString, Logger::CRITICAL);
        return false;
    }

    public static function error($data, $title = null, $category = null, int $eol = 0)  //NULL??
    {
        $resultString = static::join($data, $title, $eol);
        //\Yii::error($resultString, $category);
        Az::$app->utility->monolog->log($resultString, Logger::ERROR);
        return false;
    }

    public static function warning($data, $title = null, $category = null, int $eol = 0)
    {
        $resultString = static::join($data, $title, $eol);
        //\Yii::warning($resultString, $category);
        Az::$app->utility->monolog->log($resultString, Logger::WARNING);
        return false;
    }

    public static function info($data, $title = null, string $category = null, int $eol = 0)
    {
        $resultString = static::join($data, $title, $eol);
        //\Yii::info($resultString, $category);
        Az::$app->utility->monolog->log($resultString, Logger::INFO);
        return true;
    }


    public static function debug($data, $title = null, string $category = null, int $eol = 0)
    {
        $resultString = static::join($data, $title, $eol);
        //\Yii::debug($resultString, $category);
        // vd(trace());
        Az::$app->utility->monolog->log($resultString, Logger::DEBUG);
        return true;
    }

    public static function beginProfile($token, $category = 'application')
    {
        $resultString = static::join($token, $category);
        Az::$app->utility->monolog->log($resultString, Logger::INFO);
        parent::beginProfile($token, $category);
    }

    public static function endProfile($token, $category = 'application')
    {
        $resultString = static::join($token, $category);
        Az::$app->utility->monolog->log($resultString, Logger::INFO);
        parent::beginProfile($token, $category);
    }

    public static function trace($data, $title = null, string $category = null, int $eol = 0)
    {
        return self::debug($data, $title, $category, $eol);
    }

    #endregion


    #region Utils

    public static function exit($data, $title = null, $category = null, int $eol = 0)
    {
        $resultString = static::join($data, $title, $eol);
        \Yii::error($resultString, $category);
        return exit(100);
    }


    public static function hold($data, $title = null, $category = null, int $eol = 0)
    {
        $resultString = static::join($data, $title, $eol);
        \Yii::error($resultString, $category);
        return sleep(3600 * 24 * 7);
    }


    public static function sql($query, string $method)
    {
        $sql = Az::$app->cores->utility->getSQL($query);
        self::trace($sql, "SQL EXTRACT | {$method}", Cat_SQLQuery, 1);
    }


    public static function count($counts, string $title)
    {
        /*$title = str_replace('get', '', $title);
        return self::trace(count($counts), 'ALL ' . $title . ' Count', Cat_ArrayCount);*/
    }


    public static function eol($count = 2)
    {
        $return = '';
        for ($iI = 1; $iI <= $count; $iI++) {
            $return .= PHP_EOL;
        }

        \Yii::trace($return);
    }

    public static function echo($data, $title = null)
    {
        $end = (PHP_SAPI === 'cli') ? PHP_EOL : '<br/>';
        $end = str_repeat($end, 2);

        $resultString = static::join($data, $title);
        echo $resultString . $end;
    }


    public static function title($title): void
    {
        cli_set_process_title($title);
        self::start($title, true);
    }

    public static function start($startString, bool $action = true): void
    {

        if ($action) {
            $sRewetka = '###' . TAB;
            self::$app->params[paramMethod] = $startString;
        } else {
            $sRewetka = '#';
            self::$app->params[paramMethodMini] = $startString;
        }

        self::info($startString . PHP_EOL, PHP_EOL . PHP_EOL . $sRewetka . TAB . 'STARTING' . TAB . TAB . '=>' . TAB);
    }


    public static function end()
    {
        if (!empty(self::$app->params[paramMethod]))
            self::info(self::$app->params[paramMethod] . PHP_EOL, PHP_EOL . PHP_EOL . '###' . TAB . 'ENDING' . TAB . TAB . '=>' . TAB);
    }


    private static function join($data, $title, int $eol = 0): string
    {

        if (is_string($data))
            $resultString = $data;
        else
            $resultString = VarDumper::dumpAsString($data);

        if ($title)
            $resultString = $title . ' | ' . $resultString;

        if ($eol > 0) {
            $eolstart = str_repeat(PHP_EOL, $eol);
            $eolEnd = str_repeat(PHP_EOL, $eol);
            return $eolstart . PHP_EOL . '#' . TAB . TAB . $resultString . $eolEnd;
        } else
            return $resultString;


    }




    /**
     *
     * Function  l
     * @param $message
     * @param array $params
     * @return  mixed
     */
    public static function l($message, $params = [])
    {

        global $boot;

        if (empty($message))
            return '';

        /** @var Cores $cores */
        $cores = Az::$app->cores;

        if ($boot->isCLI())
            if ($boot->env('langCLI'))
                return $message;

        if (!$cores->langs->hasTable())
            return $message;

        /** @var Langs $lang */
        $result = $cores->langs->azl($message);


        $placeholders = null;
        if (!empty($params)) {
            foreach ((array)$params as $name => $value)
                $placeholders['{' . $name . '}'] = $value;
        }
        if ($placeholders !== null)
            $result = strtr($result, $placeholders);

        $result = str_replace("'", '', $result);

        return $result;

    }

    #endregion


}
