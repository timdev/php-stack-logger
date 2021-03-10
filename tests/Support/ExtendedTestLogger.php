<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\StackLoggerTrait;

/**
 * Demonstrates extending any old PSR-3 logger with the StackLogger trait.
 */
final class ExtendedTestLogger extends TestLogger implements TestLoggerInterface
{
    // This is all that's required to extend your PSR-3 Logger.
    use StackLoggerTrait;

    // Test-Helpers
    use TestLoggerTrait;

    public $records;
    public $recordsByLevel;

    /**
     * Replace TestLogger's $records arrays with an ArrayObject, which be copied
     * by reference during a clone, in the same way as in the real world, where
     * clones get references to the original handlers/resources/etc.
     */
    public function __construct() {
        $this->records = new \ArrayObject();
        $this->recordsByLevel = new \ArrayObject();
    }
}
