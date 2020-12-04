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

namespace zetsoft\system\except;


use yii\web\HttpException;

class ZTokenIncorrectException extends HttpException
{


	public function __construct($message = null, $code = 0, \Exception $previous = null)
	{
		if (!$message)
			$message = 'Введен некорректный токен безопасности!';

		parent::__construct(403, $message, $code, $previous);
	}
}

