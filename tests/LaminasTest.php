<?php declare(strict_types=1);

namespace TimDev\StackLogger\Test;

class LaminasTest extends BaseTest
{
    /* PHP 7.4 doesn't support covariant typed properties 😢 */
    /** @var Support\ExtendedLaminasLogger */
    protected Support\TestLoggerInterface $log;

    protected function makeTestSubject(): Support\ExtendedLaminasLogger
    {
        return new Support\ExtendedLaminasLogger();
    }


}
