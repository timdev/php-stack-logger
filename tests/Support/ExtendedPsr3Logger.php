<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Psr\Log\LoggerInterface;
use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\Psr3Logger;

/**
 * Implements TestLoggerInterface using WrappedPSR3 with Psr\Log\Test\TestLogger
 * as the underlying logger. This is just about as simple as we can get for
 * testing.
 *
 * @psalm-import-type LogRecord from TestLoggerInterface
 * @extends Psr3Logger<LoggerInterface>
 */
class ExtendedPsr3Logger extends Psr3Logger implements TestLoggerInterface
{
    // Test-Helpers
    use TestLoggerTrait;

    public function __construct()
    {
        parent::__construct(new TestLogger());
    }

    /** return array<LogRecord> */
    public function getRecords(): array
    {
        /** @var TestLogger $wrapped */
        $wrapped = $this->getWrapped();
        /** @var array<LogRecord> */
        return $wrapped->records;
    }
}
