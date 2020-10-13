<?php
declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use DateTimeZone;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use TimDev\StackLogger\StackMonologLoggerTrait;
use TimDev\StackLogger\Test\Support\TestLoggerInterface;
use TimDev\StackLogger\Test\Support\TestLoggerTrait;

/**
 * Here we extend Monolog\Logger by applying the StackMonologLoggerTrait trait, and add an extra method just for laughs.
 *
 */
class ExtendedMonologLogger extends Logger implements TestLoggerInterface
{
    use StackMonologLoggerTrait;
    use TestLoggerTrait;

    public function __construct()         
    {
        parent::__construct('test', [new TestHandler()], [], null);
    }

    /**
     * You can add your own methods on top.
     * 
     * @return bool
     */
    public function extraMethod(): bool 
    {
        return true;
    }

    public function getRecords(): array 
    {
        return $this->getHandlers()[0]->getRecords();
    }
    
//    public function recordAt(int $index): ?array 
//    {
//        return $this->getRecords()[$index] ?? null;
//    }
}
