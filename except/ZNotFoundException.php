<?php

namespace zetsoft\system\except;


use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use zetsoft\system\Az;


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
class ZNotFoundException extends NotFoundHttpException
{


    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {

        if (!$message)
            // $message = Az::l('Вам не разрешено производить данное действие');
            $message = Az::l('Страница отсутсвует!');

        parent::__construct($message, $code, $previous);

        parent::__construct($message, $code, $previous);
    }


}
