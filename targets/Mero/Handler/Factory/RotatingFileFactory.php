<?php

namespace zetsoft\system\targets\Mero\Handler\Factory;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use zetsoft\system\targets\Mero\Exception\ParameterNotFoundException;

class RotatingFileFactory extends AbstractFactory
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
                'max_files' => 0,
                'file_permission' => null,
                'filename_format' => '{filename}-{date}',
                'date_format' => 'Y-m-d',
            ],
            $this->config
        );

        $parametersRequired = ['path'];
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
        $handler = new RotatingFileHandler($this->config['path'],
            $this->config['max_files'],
            $this->config['level'],
            $this->config['bubble'],
            $this->config['file_permission']
        );
        $handler->setFilenameFormat(
            $this->config['filename_format'],
            $this->config['date_format']
        );

        return $handler;
    }
}
