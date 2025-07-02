<?php declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\Psr3StackLogger;
use TimDev\StackLogger\Test\Support\TestStackLogger;

/**
 * Test using a wrapped \Psr\Log\TestStackLogger.
 */
class Psr3Test extends BaseTestCase
{
    public function testNullLoggerFactory(): void
    {
        $null = \TimDev\StackLogger\Psr3StackLogger::makeNullLogger();
        self::assertInstanceOf(\TimDev\StackLogger\Psr3StackLogger::class, $null);
    }

    protected function makeTestSubject(): TestStackLogger
    {
        return new Psr3StackLogger();
    }
}
