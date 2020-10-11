<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\StackLoggerTrait;

/**
 * Demonstrates extending any old PSR-3 logger with the StackLogger trait.
 */
final class ExtendedTestLogger extends TestLogger
{
    // This is all that's required to extend your PSR-3 Logger.
    use StackLoggerTrait;

    
    /* 
     * Methods below are just introspection methods for use in unit tests.
     * If you're looking at this class as a usage example, you can pretend they aren't here.
     */
    public function contextAt($recordIndex)
    {
        if (!isset($this->records[$recordIndex])) {
            return false;
        }
        return $this->records[$recordIndex]['context'] ?? false;
    }
    
    public function contextKeysAt($recordIndex)
    {
        $ctx = $this->contextAt($recordIndex);
        return $ctx ? array_keys($ctx) : false;
    }
    
    public function contextValuesAt($recordIndex)
    {
        $ctx = $this->contextAt($recordIndex);
        return $ctx ? array_values($ctx) : false;
    }
    
    public function contextCountAt($recordIndex)
    {
        $ctx = $this->contextAt($recordIndex);
        return $ctx ? count($ctx) : false;
    }
}
