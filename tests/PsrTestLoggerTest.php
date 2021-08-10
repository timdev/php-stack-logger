<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\ExtendedPsr3Logger;
use TimDev\StackLogger\Test\Support\TestLoggerInterface;
use TimDev\StackLogger\Psr3Logger;

/**
 * Test using a wrapped \Psr\Log\TestLogger.
 */
class PsrTestLoggerTest extends BaseTest
{
    protected function makeTestSubject(): TestLoggerInterface
    {
        return new ExtendedPsr3Logger();
    }
}
