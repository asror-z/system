<?php
namespace zetsoft\system\monolog\processors;


use Monolog\Processor\MemoryProcessor;

class ZMemoryUsageProcessor extends MemoryProcessor
{
    public function __invoke(array $record): array
    {
        $usage = $this->formatBytes(memory_get_usage(false));
        $usageReal = $this->formatBytes(memory_get_usage(true));

        $result = $usage . '/' . $usageReal;
        $result = str_replace(' ', '', $result);

        $record['extra']['memory_usage'] = $result;

        return $record;
    }
}



