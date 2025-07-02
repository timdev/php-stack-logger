<?php declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Monolog\LogRecord as MonologLogRecord;
use TimDev\StackLogger\StackLogger;

/**
 * The unit tests expect to test loggers that implement this interface, which
 * composes PSR-3 and some helper methods for use in assertions. You can use the
 * TestLoggerTrait to get most of this. You may need to override a method or
 * two, as we do in ExtendedWrappedMonolog.
 *
 * @phpstan-type LogRecordArray = array{message:string, context:array<mixed>, channel:string, ...}
 * @phpstan-type LogRecord = LogRecordArray|MonologLogRecord
 */
interface TestStackLogger extends StackLogger
{
    /**
     * Returns an array of all messages logged to the log destination. Calling
     * getRecords() on a child logger and any of its ancestors must return the
     * same array, since they share the same destination, since in production,
     * they're all writing to the same file, logging endpoint, etc.
     *
     * This is the central method of this interface.
     *
     * @return array<LogRecord>
     */
    public function getRecords(): array;

    /* Helper methods for inspecting logged records and logger context. All of
     * these are provided by the provided by the included TestLoggerTrait. */

    /** @return mixed[] */
    public function contextAt(int $recordIndex): array;

    /** @return list<array-key> */
    public function contextKeysAt(int $recordIndex): array;

    /** @return list<mixed> */
    public function contextValuesAt(int $recordIndex): array;

    public function contextCountAt(int $recordIndex): int;

    /**
     * Returns the record at a specific index. Throws if no record
     * exists at that index.
     *
     * @return LogRecord
     */
    public function recordAt(int $index): array|MonologLogRecord;

    /* Methods for inspecting the context tracked by the instance */

    /** @return array<mixed> */
    public function getContext(): array;

    public function countContext(): int;
}
