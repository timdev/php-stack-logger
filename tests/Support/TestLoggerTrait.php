<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

/**
 * A trait that implements most of TestLoggerInterface.
 *
 * Test subjects buffer messages somehow, and implement getRecords() to expose
 * the buffered messages to the tests.
 *
 * @psalm-import-type LogRecord from TestLoggerInterface
 */
trait TestLoggerTrait
{
    /** @return LogRecord */
    public function recordAt(int $index): array
    {
        $records = $this->getRecords();
        if (!array_key_exists($index, $records)) {
            throw new \OutOfBoundsException("No record at index {$index}");
        }
        return $records[$index];
    }

    public function contextAt(int $recordIndex): array
    {
        $record = $this->recordAt($recordIndex);
        if (!is_array($record['context'] ?? null)) {
            throw new \UnexpectedValueException("Missing/invalid context at record index: {$recordIndex}");
        }
        return $record['context'];
    }

    public function contextValuesAt(int $recordIndex): array
    {
        return array_values($this->contextAt($recordIndex));
    }

    public function contextKeysAt(int $recordIndex): array
    {
        return array_keys($this->contextAt($recordIndex));
    }

    public function contextCountAt(int $recordIndex): int
    {
        return count($this->contextAt($recordIndex));
    }

    /* Methods for inspecting the context tracked by the instance */

    public function getContext(): array
    {
        return $this->mergedContext();
    }

    public function countContext(): int
    {
        return count($this->mergedContext());
    }
}
