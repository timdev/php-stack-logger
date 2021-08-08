<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\WrappedPSR3;

/**
 * @psalm-import-type LogRecord from TestLoggerInterface
 * @extends WrappedPSR3<\Psr\Log\LoggerInterface>
 */
class ExtendedWrappedPSR3 extends WrappedPSR3 implements TestLoggerInterface
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
