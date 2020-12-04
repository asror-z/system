<?php

namespace zetsoft\system\targets\ziganshinalexey\Handler\Factory;


use Monolog\Logger;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\di\Instance;
use zetsoft\system\targets\ziganshinalexey\Exception\ParameterNotFoundException;
use zetsoft\system\targets\ziganshinalexey\Handler\YiiDbHandler;

class YiiDbFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function checkParameters()
    {
        $this->config = array_merge(
            [
                'level' => Logger::DEBUG,
                'bubble' => true,
                'table' => 'logs',
            ],
            $this->config
        );

        $parametersRequired = ['reference'];
        foreach ($parametersRequired as &$parameter) {
            if (!isset($this->config[$parameter])) {
                throw new ParameterNotFoundException(
                    sprintf("Parameter '%s' not found in handler configuration", $parameter)
                );
            }
        }

        return null;
    }

    /**
     * Return a Yii2 database connection.
     *
     * @param string $reference Name of Yii2 database connection
     *
     * @return Connection
     *
     * @throws InvalidConfigException When the reference is invalid
     */
    protected function getYiiConnection($reference)
    {
        return Instance::ensure($reference, Connection::className());
    }

    /**
     * {@inheritdoc}
     */
    public function createHandler()
    {
        return new YiiDbHandler(
            $this->getYiiConnection($this->config['reference']),
            $this->config['table'],
            $this->config['level'],
            $this->config['bubble']
        );
    }
}
