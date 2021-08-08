<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Laminas\Log\Logger;
use Laminas\Log\PsrLoggerAdapter;
use Laminas\Log\Writer\Mock;
use TimDev\StackLogger\WrappedPSR3;

/**
 * An extension of the PsrLoggerAdapter from laminas-Log.
 *
 * @psalm-import-type LogRecord from TestLoggerInterface
 */
class ExtendedLaminasLogger extends WrappedPSR3 implements TestLoggerInterface
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
        /*
         * Laminas Log has a different internal record structure, but that's
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
