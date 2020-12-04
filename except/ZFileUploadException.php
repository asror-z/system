<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    09.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\except;


use zetsoft\system\Az;
use yii\base\Exception;
use yii\web\HttpException;

class ZFileUploadException extends HttpException
{

    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        if (!$message)
            $message = Az::l('Проблема с загрузкой файлов');

        parent::__construct(403, $message, $code, $previous);
    }
}
