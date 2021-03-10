<?php declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

/**
 * A trait that implements TestLoggerInterface.
 *
 * If your logger implementation has some way of buffering records as an array,
 * you can probably leverage this, overriding getRecords() as necessary.
 */
trait TestLoggerTrait
{
    public function getRecords(): array
    {
        return (array)$this->records;
    }

    public function recordAt($index): ?array
    {
        return $this->getRecords()[$index] ?? null;
    }

    public function contextAt($recordIndex)
    {
        return $this->recordAt($recordIndex)['context'] ?? null;
    }

    public function contextKeysAt($recordIndex)
    {
        $ctx = $this->contextAt($recordIndex);
        return $ctx ? array_keys($ctx) : null;
    }

    public function contextValuesAt($recordIndex)
    {
        $ctx = $this->contextAt($recordIndex);
        return $ctx ? array_values($ctx) : null;
    }

    public function contextCountAt($recordIndex)
    {
        $ctx = $this->contextAt($recordIndex);
        return $ctx ? count($ctx) : null;
    }
}
