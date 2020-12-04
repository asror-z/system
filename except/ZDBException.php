<?php

namespace zetsoft\system\except;

use yii\helpers\VarDumper;

class ZDBException extends \yii\base\Exception
{

	/**
	 * Constructor.
	 * @param string $message PDO error message
	 * @param array $errorInfo PDO error info
	 * @param int $code PDO error code
	 * @param \Exception $previous The previous exception used for the exception chaining.
	 */
	public function __construct(array $aError)
	{
		$messages = VarDumper::export($aError);
		$messages = 'ZDatabase Exception | ' . $messages;
		parent::__construct($messages, 55, null);
	}

	/**
	 * @return string the user-friendly name of this exception
	 */
	public function getName()
	{
		return 'ZDatabase Exception';
	}

}
