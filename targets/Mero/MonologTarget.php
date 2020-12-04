<?php

namespace zetsoft\system\targets\Mero;

use Yii;
use Monolog\Logger;
use yii\log\Logger as YiiLogger;
use yii\log\Target;
use zetsoft\system\Az;
use zetsoft\system\targets\Mero\Exception\ComponentNotConfiguredException;

class MonologTarget extends Target
{
    /**
     * @var MonologComponent Monolog component object
     */
    private $component;

    /**
     * @var string Monolog channel name
     */
    public $channel = 'main';

    /**
     * @var array Interpret Yii 2 levels to Monolog levels
     */
    private $monologLevels = [
        YiiLogger::LEVEL_TRACE => Logger::DEBUG,
        YiiLogger::LEVEL_PROFILE_BEGIN => Logger::DEBUG,
        YiiLogger::LEVEL_PROFILE_END => Logger::DEBUG,
        YiiLogger::LEVEL_INFO => Logger::INFO,
        YiiLogger::LEVEL_WARNING => Logger::WARNING,
        YiiLogger::LEVEL_ERROR => Logger::ERROR,
    ];

    /**
     * {@inheritdoc}
     */
    public function init()
    {
    //vdd(Az::$app->monolog);
        parent::init();
        /*if (!isset(Az::$app->monolog) || (Az::$app->monolog instanceof MonologComponent)) {
            throw new ComponentNotConfiguredException();
        }*/
        $this->component = Az::$app->monolog;
    }

    /**
     * {@inheritdoc}
     */
    public function export()
    {
        $logger = $this->component->getLogger($this->channel);

        foreach ($this->messages as $message) {
            list($text, $level, $category) = $message;
            $logger->log(
                $this->monologLevels[$level],
                "[$category] $text"
            );
        }
    }
}
