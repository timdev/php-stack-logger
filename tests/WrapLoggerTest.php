<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\ExtendedWrappedPSR3;
use TimDev\StackLogger\Test\Support\TestLoggerInterface;
use TimDev\StackLogger\WrappedPSR3;

/**
 * Test against monolog.
 *
 * Uses ExtendedMonologLogger as a subject, with additional test method(s) for
 * monolog-specific stuff.
 */
class WrapLoggerTest extends BaseTest
{
    protected function makeTestSubject(): TestLoggerInterface
    {
        return new ExtendedWrappedPSR3();
    }
}
