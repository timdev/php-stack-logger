<?php
declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use TimDev\StackLogger\MonologStackLoggerTrait;

/**
 * Here we extend Monolog\Logger by applying the StackMonologLoggerTrait trait, and add an extra method just for laughs. 
 */
class ExtendedMonologLogger extends Logger implements TestLoggerInterface
{
    use MonologStackLoggerTrait;
    use TestLoggerTrait;

    public function __construct()
    {
        parent::__construct('test', [new TestHandler()], [], null);
    }

    /**
     * Used in a test to demonstrate static analysis understands phpDoc directives in the trait.
     */
    public function extraMethod(): bool
    {
        return true;
    }

    public function getRecords(): array
    {
        return $this->getHandlers()[0]->getRecords();
    }
}
