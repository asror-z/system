<?php

namespace zetsoft\system\targets\ziganshinalexey\Handler\Factory;


use Monolog\Handler\AbstractHandler;
use zetsoft\system\targets\ziganshinalexey\Exception\ParameterNotFoundException;

abstract class AbstractFactory
{
    /**
     * @var array Configuration parameters
     */
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->checkParameters();
    }

    /**
     * Returns the class name.
     *
     * @return string Class name
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * Check configuration parameters.
     *
     * @return bool Returns validation status
     *
     * @throws ParameterNotFoundException When required parameter not found
     */
    abstract protected function checkParameters();

    /**
     * Create a handler object.
     *
     * @return AbstractHandler Handler object
     */
    abstract public function createHandler();
}
