<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\WrappedPSR3;

class ExtendedWrappedPSR3 extends WrappedPSR3 implements TestLoggerInterface
{
    // Test-Helpers
    use TestLoggerTrait;

    public function __construct()
    {
        $logger = new TestLogger();
        parent::__construct($logger);
    }

    public function getRecords(): array
    {
        /** @var TestLogger $wrapped */
        $wrapped = $this->getWrapped();
        /** @var array[] */
        return $wrapped->records;
    }
}
