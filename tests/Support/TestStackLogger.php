<?php

namespace TimDev\StackLogger\Test\Support;

use OutOfBoundsException;
use TimDev\StackLogger\StackLogger;

/**
 * The unit tests expect to test loggers that implement this interface, which
 * composes PSR-3 and some helper methods for use in assertions. You can use the
 * TestLoggerTrait to get most of this. You may need to override a method or
 * two, as we do in ExtendedWrappedMonolog.
 *
 * @psalm-type LogRecord = array{message:string, context:array, channel:string}
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
     * @psalm-return array<LogRecord>
     */
    public function getRecords(): array;

    /* Helper methods for inspecting logged records and logger context. All of
     * these are provided by the provided by the included TestLoggerTrait. */

    public function contextAt(int $recordIndex): array;

    public function contextKeysAt(int $recordIndex): array;

    public function contextValuesAt(int $recordIndex): array;

    public function contextCountAt(int $recordIndex): int;

    /**
     * Returns the record at a specific index. Throws if no record
     * exists at that index.
     *
     * @throws OutofBoundsException
     * @return LogRecord
     */
    public function recordAt(int $index): array;

    /* Methods for inspecting the context tracked by the instance */

    public function getContext(): array;

    public function countContext(): int;
}
