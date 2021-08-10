<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\ExtendedLaminasLogger;

/**
 * Test against a laminas-log PsrLoggerAdapter instance.
 */
class LaminasTest extends BaseTest
{
    protected function makeTestSubject(): ExtendedLaminasLogger
    {
        return new Support\ExtendedLaminasLogger();
    }
}
