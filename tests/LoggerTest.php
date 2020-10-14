<?php declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\ExtendedTestLogger;

/**
 * Test generic PSR-3 logger derived from use Psr\Log\Test\TestLogger
 */
class LoggerTest extends BaseTest
{
    protected function makeTestSubject(): ExtendedTestLogger
    {
        return new ExtendedTestLogger();
    }
}
