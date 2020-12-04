<?php

namespace zetsoft\system\targets\Mero\Handler\Factory;

use Monolog\Handler\SyslogUdpHandler;
use Monolog\Logger;
use zetsoft\system\targets\Mero\Exception\ParameterNotFoundException;


class SyslogUdpFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    protected function checkParameters()
    {
        $this->config = array_merge(
            [
                'level' => Logger::DEBUG,
                'port' => 512,
                'facility' => LOG_USER,
                'bubble' => true,
            ],
            $this->config
        );

        $parametersRequired = ['host'];
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
     * {@inheritdoc}
     */
    public function createHandler()
    {
        return new SyslogUdpHandler(
            $this->config['host'],
            $this->config['port'],
            $this->config['facility'],
            $this->config['level'],
            $this->config['bubble']
        );
    }
}
