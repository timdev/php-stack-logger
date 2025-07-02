<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\Psr3StackLogger as BasePsr3StackLogger;

/**
 * Implements TestStackLogger using WrappedPSR3 with Psr\Log\Test\TestLogger
 * as the underlying logger. This is just about as simple as we can get for
 * testing.
 *
 * @phpstan-import-type LogRecord from TestStackLogger
 * @extends BasePsr3StackLogger<LoggerInterface>
 */
class Psr3StackLogger extends BasePsr3StackLogger implements TestStackLogger
{
    // Test-Helpers
    use TestLoggerTrait;

    public function __construct()
    {
        parent::__construct(new TestLogger());
    }


    /**
     * @return array<LogRecord>
     */
    #[\Override]
    public function getRecords(): array
    {
        /** @var TestLogger $wrapped */
        $wrapped = $this->getWrapped();
        /** @var array<LogRecord> */
        return $wrapped->records;
    }
}
