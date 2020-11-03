<?php declare(strict_types=1);

namespace TimDev\StackLogger\Test;

/**
 * Runs the tests defined in BaseTest but with an instance based on
 * PsrLoggerAdapter from laminas-log.
 */
class LaminasTest extends BaseTest
{
    private Support\TestLoggerInterface $log;

    protected function makeTestSubject(): Support\ExtendedLaminasLogger
    {
        return new Support\ExtendedLaminasLogger();
    }
}
