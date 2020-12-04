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

class ZDeniedAccessException extends HttpException
{


	public function __construct($message = null, $code = 0, \Exception $previous = null)
	{
		if (!$message)
			$message = 'Попытка запрещенного доступа к данным другой компании!';

		parent::__construct(403, $message, $code, $previous);
	}
}

