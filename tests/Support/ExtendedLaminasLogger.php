<?php
declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Laminas\Log\Logger;
use Laminas\Log\PsrLoggerAdapter;
use Laminas\Log\Writer\Mock;
use TimDev\StackLogger\StackLoggerTrait;

/**
 * An extension of the PsrLoggerAdapter from Laminas-Log.
 */
class ExtendedLaminasLogger extends PsrLoggerAdapter implements TestLoggerInterface
{
    use StackLoggerTrait;
    use TestLoggerTrait;
    
    private Mock $writer;

    public function __construct() {
        
        $this->writer = new Mock();
        $logger = new Logger();
        $logger->addWriter($this->writer);
        parent::__construct($logger); 
    }    
    
    public function getRecords(): array 
    {
        /* 
        Laminas Log has a different internal record structure, but that's okay. We just transform them into something
        that resembles PSR/Monolog style records here. 
        */
        return array_map(function($record){
            return [
                'level' => $record['priorityName'],
                'message' => $record['message'],
                'context' => $record['extra']
            ];
        },
            $this->writer->events
        );
    }
}
