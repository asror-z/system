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

namespace zetsoft\system\targets;


use Boot;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\kernels\ZTrait;

use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\log\Target;


abstract class ZTarget extends Target
{

    private const  _isTestLog = false;


    private const _tab1 = '  ';
    private const _tab2 = '    ';
    private const _tab3 = '      ';
    private const _tab4 = '        ';
    private const _tab5 = '          ';
    private static $sAzClass = 'zetsoft\system\Az';


    use ZCoreTrait;


    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->trait();

    }


    public function formatBytes($bytes, $precision = 2, $bInterval = false)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units)-1);

        $bytes /= pow(1024, $pow);

        $sInterval = ($bInterval) ? ' ' : '';

        return round($bytes, $precision) . $sInterval . $units[$pow];
    }


    public function memoryUsage()
    {

        $sUsage = $this->formatBytes(memory_get_usage(), 0);
        $sRealUsage = $this->formatBytes(memory_get_usage(true), 0);

        return "{$sUsage}/{$sRealUsage}";
    }


    public static function filterMessages($messages, $levels = 0, $categories = [], $except = [])
    {

        /**
         *
         * Asror Zakirov
         * Processing New Items
         */

        $aNewMessages = [];

        foreach ($messages as $i => $message) {
            if ($levels && !($levels & $message[1])) {
                unset($messages[$i]);
                continue;
            }


            /**
             *
             * Process Category From Traces
             */

            if ($message[2] === 'application' || $message[2] === null) {
                $sTraceClass = null;

                foreach ($message[4] as $trace) {
                    if (!ZStringHelper::find($trace['file'], self::$sAzClass)) {
                        $sTraceClass = $trace['file'];
                        break;
                    }
                }


                $boot = new Boot();

                $sTraceClass = str_replace(array(Root . '\\', '.php'), '', $sTraceClass);

                $message[2] = 'zetsoft\\' . $sTraceClass;
            }


            /**
             *
             * Continue Processing
             */

            $matched = empty($categories);
            foreach ($categories as $category) {
                if ($message[2] === $category || (!empty($category) && substr_compare($category, '*', -1, 1) === 0 && strpos($message[2], rtrim($category, '*')) === 0)) {
                    $matched = true;
                    break;
                }
            }

            if ($matched) {
                foreach ($except as $category) {
                    $prefix = rtrim($category, '*');
                    if (($message[2] === $category || $prefix !== $category) && strpos($message[2], $prefix) === 0) {
                        $matched = false;
                        break;
                    }
                }
            }

            if ($matched) {
                $aNewMessages[] = $message;
            }

        }
        return $aNewMessages;
    }


    public function export()
    {
        $sRelMessage = '';

        $messages = array_map([$this, 'formatMessage'], $this->messages);
        foreach ($messages as $message) {
            $sRelMessage .= $message;
        }

        $sRelMessage = StringHelper::truncate($sRelMessage, 3900);

        if (self::_isTestLog)
            $this->_testLog($sRelMessage);

        return $sRelMessage;
    }


    private function _testLog(string $sText)
    {


        $sdate = Az::$app->cores->date->dateTimeFull();
        $sdate = str_replace(':', '-', $sdate);

        list($sText, $level, $category, $timestamp) = $this->messages[0];
        $level = Logger::getLevelName($level);
        $class = StringHelper::basename(get_called_class());

        if ($boot->isCLI())
            $type = 'cmd';
        else
            $type = 'web';

        $logger = Az::$app->basePath . "\\logger";

        FileHelper::createDirectory($logger);

        $sdate = "{$logger}\\{$type}_{$class}_{$level}_{$sdate}.txt";

        file_put_contents($sdate, $sText);
    }


    public function formatMessage($message)
    {
        list($sText, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($sText)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($sText instanceof \Throwable || $sText instanceof \Exception) {
                $sText = (string)$sText;
            } else {
                $sText = VarDumper::export($sText);
            }
        }

        /**
         *
         * Components
         */

        $sPrefix = $this->getMessagePrefix($message);
        $date = date('Y-m-d H:i:s', $timestamp);


        /**
         *
         * Traces
         */

        $traces = [];
        $sRelStr = PHP_EOL;

        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                if (!ZStringHelper::find($trace['file'], self::$sAzClass))
                    $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        $tabChar = self::_tab4 . '#' . self::_tab2;
        $sRelStr .= $sText;

        if (!empty($traces)) {
            $sRelStr .= PHP_EOL;
            foreach ($traces as $trace) {
                $sRelStr .= PHP_EOL . $tabChar . $trace;
            }
        }


        /**
         *
         * Replaces
         */

        $sRelStr = str_replace(array(Az::getAlias('@root'), 'Stack trace:'), '', $sRelStr);


        $sPattern = '/\#\d+ (.*)/i';
        $replacement = $tabChar . '$1';
        $sRelStr = preg_replace($sPattern, $replacement, $sRelStr);


        /**
         *
         * Add Suffix
         */


        /*
                $sRelStr .= PHP_EOL . PHP_EOL;
                $sRelStr .= 'Level: '. strtoupper($level) . ' | ';
                $sRelStr .= 'Category: '. $category . ' | ';
                $sRelStr .= 'DateTime: '. $date . ' | ';
                $sRelStr .= 'Server Name: '. getenv('Computername');
                $sRelStr .= PHP_EOL;
        */

        $sRelStr .= PHP_EOL . PHP_EOL;
        $sRelStr .= strtoupper($level) . ' | ';
        $sRelStr .= $category . ' | ';
        $sRelStr .= Az::$app->cores->date->dateTime() . ' | ';
        $sRelStr .= getenv('Computername') . ' | ';
        $sRelStr .= $this->memoryUsage();
        $sRelStr .= PHP_EOL;

        if (!$boot->isCLI()) {
            $sWebURL = Az::$app->request->absoluteUrl;
            $sWebUserName = Az::$app->cores->auth->identity->Username;
            $sWebUserIP = $boot->ipUser;

            /*
                        $sRelStr .= 'URL: '. $sWebURL . ' | ';
                        $sRelStr .= 'User IP: '. $sWebUserIP . ' | ';
                        $sRelStr .= (!empty($sWebUserName)) ? 'User Name: '.    $sWebUserName : '';
                        $sRelStr .= PHP_EOL;
            */


            $sRelStr .= $sWebURL . ' | ';
            $sRelStr .= (!empty($sWebUserName)) ? $sWebUserName . ' | ' : '';
            $sRelStr .= $sWebUserIP;
            $sRelStr .= PHP_EOL;
        }


        return $sRelStr;
    }


}
