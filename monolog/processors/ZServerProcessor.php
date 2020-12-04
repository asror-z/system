<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\monolog\processors;


use Monolog\Processor\ProcessorInterface;
use zetsoft\system\Az;

class ZServerProcessor implements ProcessorInterface
{
    public function __invoke(array $record): array
    {
        global $boot;

        $ipALL = $boot->ipALL();
        
        $nameUser = $boot->nameUser;
        $namePC = $boot->namePC;

        $record['extra']['SERVERDATA']['ipALL'] = $ipALL;
        $record['extra']['SERVERDATA']['nameUser'] = $nameUser;
        $record['extra']['SERVERDATA']['namePC'] = $namePC;


        return $record;
    }
}
