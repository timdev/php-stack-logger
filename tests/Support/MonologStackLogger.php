<?php declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Monolog\Handler\TestHandler;
use Monolog\Logger as MonologLogger;
use Monolog\LogRecord as MonologRecord;
use TimDev\StackLogger\MonologStackLogger as BaseMonologStackLogger;

/**
 * TestLoggerInterface implementation that extends WrappedMonolog.
 */
class MonologStackLogger extends BaseMonologStackLogger implements TestStackLogger
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
            new MonologLogger('test', [$this->handler]),
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
     * @return list<MonologRecord>
     */
    public function getChannelRecords(): array
    {
        return array_values(
            array_filter(
                $this->getRecords(),
                fn(MonologRecord $rec) => $rec['channel'] === $this->getWrapped()->getName(),
            ),
        );
    }

    public function channelRecordAt(int $index): MonologRecord
    {
        return $this->getChannelRecords()[$index] ?? throw new \OutOfBoundsException("No record at index {$index}");
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
