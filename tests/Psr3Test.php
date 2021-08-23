<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\Psr3StackLogger;
use TimDev\StackLogger\Test\Support\TestStackLogger;

/**
 * Test using a wrapped \Psr\Log\TestStackLogger.
 */
class Psr3Test extends BaseTest
{
    protected function makeTestSubject(): TestStackLogger
    {
        return new Psr3StackLogger();
    }
}
