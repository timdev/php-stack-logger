<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Monolog\Handler\TestHandler;
use Monolog\Logger as MonologLogger;
use TimDev\StackLogger\Monolog;

/**
 * TestLoggerInterface implementation that extends WrappedMonolog.
 *
 * @psalm-type MonologRecord = array{message:string, context:array, channel:string}
 */
class ExtendedMonolog extends Monolog implements TestLoggerInterface
{
    use TestLoggerTrait;

    /**
     * We keep a reference to the monolog Handler here so we can get at the
     * records it stores.
     */
    protected TestHandler $handler;

    public function __construct()
    {
        $this->handler = new TestHandler();
        parent::__construct(
            new MonologLogger('test', [$this->handler])
        );
    }

    /** @return array<MonologRecord> */
    public function getRecords(): array
    {
        return $this->handler->getRecords();
    }

    /**
     * Returns the records written to this logger's channel. Child/cloned
     * loggers inherit handlers from their parent, but it's useful in some tests
     * to inspect log messages written by a particular instance.
     *
     * @return array<MonologRecord>
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
