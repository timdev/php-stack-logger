<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Laminas\Log\Logger;
use Laminas\Log\PsrLoggerAdapter;
use Laminas\Log\Writer\Mock;
use TimDev\StackLogger\Psr3StackLogger;

/**
 * Implements TestLoggerInterface by wrapping a \Laminas\Log\PsrLoggerAdapter.
 * PsrLoggerAdapter decorates \Laminas\Log\Logger to make it PSR3 compatible.
 *
 * @psalm-import-type LogRecord from TestStackLogger
 */
class LaminasStackLogger extends Psr3StackLogger implements TestStackLogger
{
    use TestLoggerTrait;

    private Mock $writer;

    public function __construct()
    {
        $this->writer = new Mock();
        $logger = new Logger();
        $logger->addWriter($this->writer);
        $logger = new PsrLoggerAdapter($logger);
        parent::__construct($logger);
    }

    public function getRecords(): array
    {
        /* Laminas Log has a different internal record structure, but that's
         * okay. We just transform them into something that resembles
         * PSR/Monolog style records here.
         */
        /** @var array<LogRecord> */
        return array_map(
            static function (array $record) {
                return [
                    'level' => $record['priorityName'],
                    'message' => $record['message'],
                    'context' => $record['extra'],
                    'channel' => '' // channel is only here to make psalm happy.
                ];
            },
            $this->writer->events
        );
    }
}
