<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Monolog\Handler\TestHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\WrappedMonolog;
use TimDev\StackLogger\WrappedPSR3;

class ExtendedWrappedMonolog extends WrappedMonolog implements TestLoggerInterface
{
    // Test-Helpers
    use TestLoggerTrait;

    protected TestHandler $handler;

    public function __construct()
    {
        $this->handler = new TestHandler();
        parent::__construct(
            new MonologLogger('test', [$this->handler])
        );
    }

    /** @return array[] */
    public function getRecords(): array
    {
        return $this->handler->getRecords();
    }

    /**
     * Returns the records written to this logger's channel. Child/cloned
     * loggers inherit handlers from their parent, but it's useful in some tests
     * to inspect log messages written by a particular instance.
     *
     * @return array[]
     */
    public function getChannelRecords(): array
    {
        return array_values(
            array_filter(
                $this->getRecords(),
                fn(array $rec) => $rec['channel'] === $this->getWrapped()->getName()
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
