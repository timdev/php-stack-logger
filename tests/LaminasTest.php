<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\TestLoggerInterface;

/**
 * Runs the tests defined in BaseTest but with an instance based on
 * PsrLoggerAdapter from laminas-log.
 */
class LaminasTest extends BaseTest
{
    protected function makeTestSubject(): TestLoggerInterface
    {
        return new Support\ExtendedLaminasLogger();
    }
}
