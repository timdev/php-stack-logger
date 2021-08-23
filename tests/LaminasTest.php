<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\LaminasStackLogger;

/**
 * Test against a laminas-log PsrLoggerAdapter instance.
 */
class LaminasTest extends BaseTest
{
    protected function makeTestSubject(): LaminasStackLogger
    {
        return new Support\LaminasStackLogger();
    }
}
