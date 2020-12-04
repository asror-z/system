<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\except;

use Yii;
use yii\base\UserException;
use yii\web\ErrorHandler;
use yii\web\Response;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\widgets\former\ZDynaWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;

class ZErrorHandler extends ErrorHandler
{


    public function handleError($code, $message, $file, $line)
    {
        return parent::handleError($code, $message, $file, $line);
    }

    public function handleException($exception)
    {
        return parent::handleException($exception);
    }

    public function handleFatalError()
    {
        parent::handleFatalError();


    }
    public function renderCallStackItem($file, $line, $class, $method, $args, $index)
    {
        $lines = [];
        $begin = $end = 0;
        if ($file !== null && $line !== null) {
            $line--; // adjust line number from one-based to zero-based
            $lines = @file($file);
            if ($line < 0 || $lines === false || ($lineCount = count($lines)) < $line) {
                return '';
            }

            $half = (int) (($index === 1 ? $this->maxSourceLines : $this->maxTraceSourceLines) / 2);
            $begin = $line - $half > 0 ? $line - $half : 0;
            $end = $line + $half < $lineCount ? $line + $half : $lineCount - 1;
        }

        return Az::$app->require($this->callStackItemView, [
            'file' => $file,
            'line' => $line,
            'class' => $class,
            'method' => $method,
            'index' => $index,
            'lines' => $lines,
            'begin' => $begin,
            'end' => $end,
            'args' => $args,
        ]);
    }

    /**
     * Renders call stack.
     * @param \Exception|\ParseError $exception exception to get call stack from
     * @return string HTML content of the rendered call stack.
     * @since 2.0.12
     */
    public function renderCallStack($exception)
    {
        $out = '<ul>';
        $out .= $this->renderCallStackItem($exception->getFile(), $exception->getLine(), null, null, [], 1);
        for ($i = 0, $trace = $exception->getTrace(), $length = count($trace); $i < $length; ++$i) {
            $file = !empty($trace[$i]['file']) ? $trace[$i]['file'] : null;
            $line = !empty($trace[$i]['line']) ? $trace[$i]['line'] : null;
            $class = !empty($trace[$i]['class']) ? $trace[$i]['class'] : null;
            $function = null;
            if (!empty($trace[$i]['function']) && $trace[$i]['function'] !== 'unknown') {
                $function = $trace[$i]['function'];
            }
            $args = !empty($trace[$i]['args']) ? $trace[$i]['args'] : [];
            $out .= $this->renderCallStackItem($file, $line, $class, $function, $args, $i + 2);
        }
        $out .= '</ul>';
        return $out;
    }

    protected function handleFallbackExceptionMessage($exception, $previousException)
    {
        $response = new Response();
        $response->setStatusCodeByException($exception);

        $response->data = $this->renderFile($this->exceptionView, [
            'handler' => $exception,
            'exception' => $exception,
        ]);

        $response->send();
    }

    protected function renderException($exception)
    {
    
        $response = new Response();
        $data = Az::$app->cores->auth->require('/webhtm/'.$this->errorAction . '.php');
        $response->data = $data;

        $response->send();

    }
}
