<?php
/**
 * @link http://www.yiiframework.com/
 * @license http://www.yiiframework.com/license/
 */

namespace zetsoft\system\targets;

use zetsoft\system\targets\ZTarget;
use yii\helpers\Console;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\log\Target;

/**
 * ConsoleTarget writes log to console (useful for debugging console applications)
 *
 * @author pahanini <pahanini@gmail.com>
 */
class ZConsoleTarget extends ZTarget
{
	/**
	 * @var bool If true context message will be added to the end of output
	 */
	public $enableContextMassage = true;

	public $isDisplayCategory = true;

	public $isDisplayDate = true;
	
	public $isDisplayMemory = true;
	
	public $isDisplayLevel = true;

	public $padSize = 30;



	/**
	 * @var array color scheme for message labels
	 */
	public $color = [
		'error' => Console::BG_RED
	];

	/**
	 * @inheritdoc
	 */
	public function export()
	{

		foreach ($this->messages as $message) {
			if ($message[1] == Logger::LEVEL_ERROR || $message[1] == Logger::LEVEL_WARNING) {
				Console::error($this->formatMessage($message));
			} else {
				Console::output($this->formatMessage($message));
			}
		}
	}

	/**
	 * @param array $message
	 * 0-massage
	 * 1-level
	 * 2-category
	 * 3-timestamp
	 * 4-???
	 *
	 * @return string
	 */
	public function formatMessage($message)
	{

		$label = $this->generateLabel($message);
		$text = $this->generateText($message);

		if ($this->isDisplayDate && $this->isDisplayCategory)
			$this->padSize = 60;

		if ($this->isDisplayDate && !$this->isDisplayCategory)
			$this->padSize = 40;

		return str_pad($label, $this->padSize, ' ') . ' ' . $text;
	}

	/**
	 * @param $message
	 *
	 * @return string
	 */
	private function generateLabel($message)
	{
		$label = '';


		//Add date to log
		if (true == $this->isDisplayDate) {
			$label .= '[' . Az::$app->cores->date->dateTimeFull() . ']';
		}
		//Add category to label
		if ($this->isDisplayCategory === true) {
			$label .= "[" . $message[2] . "]";
		}

		if (true === $this->isDisplayMemory) {
			$label .= "[" . $this->memoryUsage() . "]";
		}



		if ($this->isDisplayLevel) {
			$level = Logger::getLevelName($message[1]);

			$tmpLevel = "[$level]";

			if (Console::streamSupportsAnsiColors(\STDOUT)) {
				if (isset($this->color[$level])) {
					$tmpLevel = Console::ansiFormat($tmpLevel, [$this->color[$level]]);
				} else {
					$tmpLevel = Console::ansiFormat($tmpLevel, [Console::BOLD]);
				}
			}
			$label .= $tmpLevel;
		}
		


		return $label;
	}

	/**
	 * @param $message
	 *
	 * @return string
	 */
	private function generateText($message)
	{
		$text = $message[0];
		if (!is_string($text)) {
			// exceptions may not be serializable if in the call stack somewhere is a Closure
			if ($text instanceof \Throwable || $text instanceof \Exception) {
				$text = (string)$text;
			} else {
				$text = VarDumper::export($text);
			}
		}
		return $text;
	}

	/**
	 * @inheritdoc
	 * @return string
	 */
	protected function getContextMessage()
	{
		return $this->enableContextMassage ? parent::getContextMessage() : '';
	}
}
