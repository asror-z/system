<?php

namespace zetsoft\system\targets\ziganshinalexey\Handler\Factory;

use Monolog\Handler\SlackHandler;
use Monolog\Logger;
use zetsoft\system\targets\ziganshinalexey\Exception\ParameterNotFoundException;

class SlackFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function checkParameters()
    {
        $this->config = array_merge(
            [
                'level' => Logger::DEBUG,
                'username' => 'Monolog',
                'useAttachment' => true,
                'iconEmoji' => ':computer:',
                'bubble' => true,
                'useShortAttachment' => false,
                'includeContextAndExtra' => false,
            ],
            $this->config
        );

        $parametersRequired = ['token', 'channel'];
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
        return new SlackHandler(
            $this->config['token'],
            $this->config['channel'],
            $this->config['username'],
            $this->config['useAttachment'],
            $this->config['iconEmoji'],
            $this->config['level'],
            $this->config['bubble'],
            $this->config['useShortAttachment'],
            $this->config['includeContextAndExtra']
        );
    }
}
