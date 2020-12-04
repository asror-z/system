<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 */

namespace Monolog\Processor;


class ZProcessIdProcessor implements ProcessorInterface
{
    public function __invoke(array $record): array
    {
        $record['extra']['process_id'] = getmypid();

        return $record;
    }
}
