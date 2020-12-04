<?php

namespace zetsoft\system\except;


use yii\web\ForbiddenHttpException;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;


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
class ZForbiddenException extends ForbiddenHttpException
{



	public function __construct($message = null, $code = 0, \Exception $previous = null)
	{



       // $this->trait();

     
		if (!$message)
			// $message = Az::l('Вам не разрешено производить данное действие');
			$message = Az::l('Ваша роль в системе, не разрешает выполнить данное действие!');

		parent::__construct($message, $code, $previous);
	}


}
