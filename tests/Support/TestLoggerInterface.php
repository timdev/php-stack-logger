<?php

namespace TimDev\StackLogger\Test\Support;

use OutOfBoundsException;
use TimDev\StackLogger\LoggerInterface;

/**
 * The unit tests expect to test loggers that implement this interface, which
 * composes PSR-3 and some helper methods for use in assertions. You can use the
 * TestLoggerTrait to get most of this. You may need to override a method or
 * two, as we do in ExtendedMonologLogger
 *
 * @psalm-type LogRecord = array{message:string, context:array, channel:string}
 */
interface TestLoggerInterface extends LoggerInterface
{
    /* Methods for inspecting accumulated records */

    public function contextAt(int $recordIndex): array;

    public function contextKeysAt(int $recordIndex): array;

    public function contextValuesAt(int $recordIndex): array;

    public function contextCountAt(int $recordIndex): int;

    /**
     * @psalm-return array<LogRecord>
     */
    public function getRecords(): array;

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
