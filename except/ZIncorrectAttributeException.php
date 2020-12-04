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
use zetsoft\system\Az;

class ZIncorrectAttributeException extends HttpException
{

	public function __construct($message = null, $code = 0, \Exception $previous = null)
	{
		if (!$message)
			$message = Az::l('Incorrect Attribute Detected');

		parent::__construct(404, $message, $code, $previous);
	}
}

