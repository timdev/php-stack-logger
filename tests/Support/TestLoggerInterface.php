<?php

namespace TimDev\StackLogger\Test\Support;

use TimDev\StackLogger\LoggerInterface;

/**
 * The unit tests expect to test loggers that implement this interface, which composes PSR-3 and some helper methods for
 * use in assertions. You can use the TestLoggerTrait to get most of this. You may need to override a method or two, as
 * we do in ExtendedMonologLogger
 */
interface TestLoggerInterface extends LoggerInterface
{
    public function contextAt($recordIndex);

    public function contextKeysAt($recordIndex);

    public function contextValuesAt($recordIndex);

    public function contextCountAt($recordIndex);
    
    public function getRecords(): array;
    
    public function recordAt(int $index): ?array;
}
