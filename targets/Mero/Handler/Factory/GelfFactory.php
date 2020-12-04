<?php

namespace zetsoft\system\targets\Mero\Handler\Factory;

use Monolog\Handler\GelfHandler;
use Monolog\Logger;
use zetsoft\system\targets\Mero\Exception\ParameterNotFoundException;


class GelfFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    protected function checkParameters()
    {
        $this->config = array_merge(
            [
                'level' => Logger::DEBUG,
                'bubble' => true,
            ],
            $this->config
        );

        $parametersRequired = ['publisher'];
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
        return new GelfHandler(
            $this->config['publisher'],
            $this->config['level'],
            $this->config['bubble']
        );
    }
}
