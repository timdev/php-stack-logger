<?php
declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use TimDev\StackLogger\MonologStackLoggerTrait;

/**
 * Here we extend Monolog\Logger by applying the StackMonologLoggerTrait trait,
 * add some test-support methods, and add an extra method just for fun (and to
 * help figure out some static analysis stuff in MonologTest.php)
 */
class ExtendedMonologLogger extends Logger implements TestLoggerInterface
{
    use MonologStackLoggerTrait;
    use TestLoggerTrait;

    public function __construct()
    {
        parent::__construct('test', [new TestHandler()], [], null);
    }

    public function getRecords(): array
    {
        if (isset($this->handlers[0]) && $this->handlers[0] instanceof TestHandler)
            return $this->handlers[0]->getRecords();

        throw new \LogicException('Badly constructed test-logger. Must have a single TestHandler.');
    }

    /**
     * Returns the records written to this logger's channel. Child/cloned
     * loggers inherit handlers from their parent, but it's useful in some tests
     * to inspect log messages written by a particular instance.
     */
    public function getChannelRecords(): array
    {
        return array_values(
            array_filter(
                $this->getRecords(),
                fn(array $rec) => $rec['channel'] === $this->getName()
            )
        );
    }

    public function channelRecordAt(int $index): ?array
    {
        return $this->getChannelRecords()[$index] ?? null;
    }
    /**
     * Used in a test to demonstrate static analysis understands phpDoc
     * directives in the trait.
     */
    public function extraMethod(): bool
    {
        return true;
    }
}
