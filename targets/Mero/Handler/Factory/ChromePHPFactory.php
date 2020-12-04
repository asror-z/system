<?php

namespace zetsoft\system\targets\Mero\Handler\Factory;

use Monolog\Handler\ChromePHPHandler;
use Monolog\Logger;


class ChromePHPFactory extends AbstractFactory
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

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function createHandler()
    {
        return new ChromePHPHandler(
            $this->config['level'],
            $this->config['bubble']
        );
    }
}
