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

use Monolog\Processor\WebProcessor;

class ZWebProcessor extends WebProcessor
{
    /**
     * Default fields
     *
     * Array is structured as [key in record.extra => key in $serverData]
     *
     * @var array
     */
    protected $extraFields = [
        'SERVER'       => 'SERVER_NAME',
        'HTTP_METHOD'  => 'REQUEST_METHOD',
        'URL'          => 'REQUEST_URI',
        'REFERRER'     => 'HTTP_REFERER',
    ];

    public function __invoke(array $record): array
    {
        // skip processing if for some reason request data
        // is not present (CLI or wonky SAPIs)

        if (!isset($this->serverData['REQUEST_URI'])) {
            return $record;
        }

        $record['extra'] = $this->appendExtraFields($record['extra']);

        return $record;
    }

    private function appendExtraFields(array $extra): array
    {
        foreach ($this->extraFields as $extraName => $serverName) {
            $extra['WEBDATA'][$extraName] = $this->serverData[$serverName] ?? null;
        }

        if (isset($this->serverData['UNIQUE_ID'])) {
            $extra['WEBDATA']['unique_id'] = $this->serverData['UNIQUE_ID'];
        }

        return $extra;
    }

}
