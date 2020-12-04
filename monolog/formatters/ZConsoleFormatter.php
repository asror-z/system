<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\monolog\formatters;


use Monolog\Formatter\NormalizerFormatter;
use zetsoft\service\utility\Monolog;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZVarDumper;

class ZConsoleFormatter extends ZLineFormatter
{
    protected $trace;

    public function __construct(?string $format = null, ?string $dateFormat = null, bool $isExtra = false, bool $isTrace = true, array $trace = null)
    {
        if ($trace === null)
            $this->trace = Monolog::getTrace(Monolog::$traceLevel);
        else
            $this->trace = $trace;
        parent::__construct($format, $dateFormat, $isExtra, $isTrace, $trace);
    }
    /**
     * {@inheritdoc}
     */
    public function format(array $record): string
    {

        global $boot;

        $vars = NormalizerFormatter::format($record);

        $output = $this->format;

        if ($this->ignoreEmptyContextAndExtra) {
            if (empty($vars['context'])) {
                unset($vars['context']);
                $output = str_replace('%context%', '', $output);
            }

            if (empty($vars['extra'])) {
                unset($vars['extra']);
                $output = str_replace('%extra%', '', $output);
            }
        }

        $processors = '';
        $serverData = '';
        foreach ($vars['extra'] as $key1 => $value1) {
            if ($key1 === 'SERVERDATA') {
                foreach ($value1 as $k => $v) {
                    if ($v !== null) {
                        $serverData .= $v . ' | ';
                    }
                }
                $serverData = rtrim($serverData, '| ');
                unset($vars['extra']['SERVERDATA']);
            }
            if (!is_array($value1)) {
                $processors .= $value1 . ' | ';
                unset($vars['extra'][$key1]);
            }
        }
        $processors = trim($processors,'| ');

        $traceArray = $this->trace;
        $traceString = '#in   ';
        if (!empty($traceArray)) {
            foreach ($traceArray as $key => $value) {
                foreach ($value as $k => $v) {
                    if ($k !== 'type' && $k !== 'class') {
                        if ($k === 'file')
                            $traceString .= $v . ':';
                        elseif ($k === 'function')
                            $traceString .= $v . '()' . ' | ';
                        else
                            $traceString .= $v . ' | ';
                    }
                }
                $traceString = rtrim($traceString, '| ');
                $traceString .= "\n#in   ";
            }
            $traceString = rtrim($traceString, "#in   ");
        }


        $vars['trace'] = $traceString;
        $vars['processors'] = $processors;
        $vars['serverdata'] = $serverData;
        $vars['prefix'] = $boot->isCLI() ? 'CMD' : 'WEB';

        if (!$this->isExtra) {
            unset($vars['extra']);
            $output = str_replace("%extra%\n", '', $output);
        }
        if (empty($this->trace)) {
            unset($vars['trace']);
            $output = str_replace("%trace%\n", '', $output);
        }
        if (!$this->isTrace && isset($vars['trace'])) {
            unset($vars['trace']);
            $output = str_replace("%trace%\n", '', $output);
        }
        if ($vars['serverdata'] === '') {
            unset($vars['serverdata']);
            $output = str_replace("%serverdata%\n", '', $output);
        }


        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%' . $var . '%')) {
                $output = str_replace('%' . $var . '%', $this->stringify($val), $output);
            }
        }


        // remove leftover %extra.xxx% and %context.xxx% if any
        if (false !== strpos($output, '%')) {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
        }

        return $output;
    }
}
