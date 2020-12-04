<?php

namespace zetsoft\system\monolog\formatters;

use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Utils;
use SoapFault;
use zetsoft\service\utility\Monolog;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZVarDumper;
use function GuzzleHttp\Psr7\str;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */
class ZLineFormatter extends LineFormatter
{
    protected $isExtra;
    protected $isTrace;
    protected $trace;

    /**
     * @param string|null $format                     The format of the message
     * @param string|null $dateFormat                 The format of the timestamp: one supported by DateTime::format
     * @param bool          $isExtra      Is enabled extre data
     * @param array         $trace        Stack trace
     * @param bool          $isTrace      Is enabled trace
     */
    public function __construct(?string $format = null, ?string $dateFormat = null, bool $isExtra = false, bool $isTrace = true, array $trace = null)
    {
        $this->isExtra = $isExtra;
        $this->isTrace = $isTrace;
        if ($trace === null)
            $this->trace= Monolog::getTrace(Monolog::$traceLevel);
        else
            $this->trace = $trace;
        parent::__construct($format, $dateFormat);
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

        $processors = ' ';
        $webData = '';
        $serverData = '';

        foreach ($vars['extra'] as $key1 => $value1) {
            if ($key1 === 'WEBDATA') {
                $webData .= '#   web ';
                foreach ($value1 as $k => $v) {
                    if ($v !== null) {
                        if(is_array($v)) {
                            $webData .= ZVarDumper::dumpAsString($v);
                        } else
                            $webData .= $v . ' | ';
                    }
                }
                $webData = rtrim($webData, '| ');
                unset($vars['extra']['WEBDATA']);
            }
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
        $processors = rtrim($processors, ' |');

        $traceArray = $this->trace;
        $traceString = '#   in ';
        if (!empty($traceArray))  {
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
                $traceString .= "\n#   in ";
            }
            $traceString = rtrim($traceString, "\n#in   ");
            $traceString = str_replace(Root, '', $traceString);
        }

        $vars['app'] = '#'. App;
        $vars['trace'] = $traceString;
        $vars['processors'] = $processors;
        $vars['serverdata'] = $serverData;
        $vars['webdata'] = $webData;
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
        if ($vars['webdata'] === '') {
            unset($vars['webdata']);
            $output = str_replace("%webdata%\n\n", '', $output);
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

    public function stringify($value): string
    {
        return $this->convertToString($value);
    }

    /**
     * @suppress PhanParamSignatureMismatch
     */
    protected function normalizeException(\Throwable $e, int $depth = 0): string
    {
        $str = $this->formatException($e);

        if ($previous = $e->getPrevious()) {
            do {
                $str .= "\n[previous exception] " . $this->formatException($previous);
            } while ($previous = $previous->getPrevious());
        }

        return $str;
    }

    protected function convertToString($data): string
    {
        if (null === $data || is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string)$data;
        }

        return ZVarDumper::export($data);
    }

    private function formatException(\Throwable $e): string
    {
        $str = '[object] (' . Utils::getClass($e) . '(code: ' . $e->getCode();
        if ($e instanceof SoapFault) {
            if (isset($e->faultcode)) {
                $str .= ' faultcode: ' . $e->faultcode;
            }

            if (isset($e->faultactor)) {
                $str .= ' faultactor: ' . $e->faultactor;
            }

            if (isset($e->detail) && (is_string($e->detail) || is_object($e->detail) || is_array($e->detail))) {
                $str .= ' detail: ' . (is_string($e->detail) ? $e->detail : reset($e->detail));
            }
        }
        $str .= '): ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine() . ')';

        if ($this->includeStacktraces) {
            $str .= "\n[stacktrace]\n" . $e->getTraceAsString() . "\n";
        }

        return $str;
    }
}
