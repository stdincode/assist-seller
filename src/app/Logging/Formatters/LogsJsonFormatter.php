<?php

namespace App\Logging\Formatters;

use Monolog\Formatter\FormatterInterface;

class LogsJsonFormatter implements FormatterInterface
{
    public function formatBatch(array $records)
    {
        foreach ($records as $key => $record) {
            $records[$key] = $this->format($record);
        }

        return $records;
    }

    public function format(array $record)
    {
        $toWrite = [
            'timestamp' => (new \DateTime())->format('Y-m-d\TH:i:s.v\Z'),
            'level'     => $record['level_name'],

            'message' => $record['message'],
            'context' => $record['context'],
        ];

        return json_encode($toWrite, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
}
