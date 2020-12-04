<?php

namespace zetsoft\system\targets\ziganshinalexey\Handler;


use Monolog\Logger;
use yii\base\BaseObject;
use zetsoft\system\targets\Mero\Handler\Factory\RotatingFileFactory;
use zetsoft\system\targets\ziganshinalexey\Exception\HandlerNotFoundException;
use zetsoft\system\targets\ziganshinalexey\Exception\ParameterNotFoundException;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\AbstractFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\BrowserConsoleFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\ChromePHPFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\FirePHPFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\GelfFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\SlackFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\SocketFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\StreamFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\SyslogFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\SyslogUdpFactory;
use zetsoft\system\targets\ziganshinalexey\Handler\Factory\YiiDbFactory;

class Strategy extends BaseObject
{
    /**
     * @var array Handler factory collection
     */
    protected $factories = [];

    public function __construct()
    {
        $this->factories = [
            'stream' => StreamFactory::className(),
            'firephp' => FirePHPFactory::className(),
            'browser_console' => BrowserConsoleFactory::className(),
            'gelf' => GelfFactory::className(),
            'chromephp' => ChromePHPFactory::className(),
            'rotating_file' => RotatingFileFactory::className(),
            'yii_db' => YiiDbFactory::className(),
            'slack' => SlackFactory::className(),
            'elasticsearch' => '',
            'fingers_crossed' => '',
            'filter' => '',
            'buffer' => '',
            'deduplication' => '',
            'group' => '',
            'whatfailuregroup' => '',
            'syslog' => SyslogFactory::className(),
            'syslogudp' => SyslogUdpFactory::className(),
            'swift_mailer' => '',
            'socket' => SocketFactory::className(),
            'pushover' => '',
            'raven' => '',
            'newrelic' => '',
            'cube' => '',
            'amqp' => '',
            'error_log' => '',
            'null' => '',
            'test' => '',
            'debug' => '',
            'loggly' => '',
            'logentries' => '',
            'flowdock' => '',
            'rollbar' => '',
        ];
    }

    /**
     * Method set handler factory collection.
     *
     * @param array $value new value.
     *
     * @return static
     */
    public function setFactories(array $value): self
    {
        $this->factories = $value;

        return $this;
    }

    /**
     * Verifies that the factory class exists.
     *
     * @param string $type Name of type
     *
     * @return bool
     *
     * @throws HandlerNotFoundException When handler factory not found
     * @throws \BadMethodCallException  When handler not implemented
     */
    protected function hasFactory($type)
    {
    
        if (! array_key_exists($type, $this->factories)) {
            throw new HandlerNotFoundException(
                sprintf("Type '%s' not found in handler factory", $type)
            );
        }
        $factoryClass = &$this->factories[$type];
        if (! class_exists($factoryClass)) {
            throw new \BadMethodCallException(
                sprintf("Type '%s' not implemented", $type)
            );
        }

        return true;
    }

    /**
     * Create a factory object.
     *
     * @param array $config Configuration parameters
     *
     * @return AbstractFactory Factory object
     *
     * @throws ParameterNotFoundException When required parameter not found
     */
    public function createFactory(array $config)
    {
        if (! array_key_exists('type', $config)) {
            throw new ParameterNotFoundException(
                sprintf("Parameter '%s' not found in handler configuration", 'type')
            );
        }
        $this->hasFactory($config['type']);
        if (isset($config['level'])) {
            $config['level'] = Logger::toMonologLevel($config['level']);
        }

        $factoryClass = &$this->factories[$config['type']];

        return new $factoryClass($config);
    }
}
