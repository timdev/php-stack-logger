<?php declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use TimDev\StackLogger\LoggerInterface;

/**
 * A trait that implements TestLoggerInterface.
 *
 * If your logger implementation has some way of buffering records as an array,
 * you can probably leverage this, overriding getRecords() as necessary.
 */
trait TestLoggerTrait
{

    /* Methods for inspecting accumulated records */

    public function getRecords(): array
    {
                return (array)$this->records;
    }

    public function recordAt($index): ?array
    {
        return $this->getRecords()[$index] ?? null;
    }

    public function contextAt($recordIndex): ?array
    {
        return $this->recordAt($recordIndex)['context'] ?? null;
    }

    public function contextKeysAt($recordIndex): ?array
    {
        $ctx = $this->contextAt($recordIndex);
        return $ctx ? array_keys($ctx) : null;
    }

    public function contextValuesAt($recordIndex): ?array
    {
        $ctx = $this->contextAt($recordIndex);
        return $ctx ? array_values($ctx) : null;
    }

    public function contextCountAt($recordIndex): ?array
    {
        $ctx = $this->contextAt($recordIndex);
        return $ctx ? count($ctx) : null;
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
