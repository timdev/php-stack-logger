<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test\Support;

use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\StackLoggerTrait;

/**
 * Extends the PSR-3 TestLogger with StackLogger.
 *
 * Demonstrates extending any old PSR-3 logger with the StackLogger trait.
 */
final class ExtendedTestLogger extends TestLogger implements TestLoggerInterface
{
    // This is all that's required to extend your PSR-3 Logger.
    use StackLoggerTrait;

    // Test-Helpers
    use TestLoggerTrait;

    /*
     \Psr\Log\Test\TestLogger uses arrays internally, which is kind of a pain,
     since our other test loggers use some kind of object. Since arrays are
     copy-by-value, this causes various recordAt() calls in BaseTest to
     conflict. So we abuse static here to ensure that *within a single test
     method*, all loggers write messages to the same array. Note that
     ExtendedTestLogger::resetRecords() in LoggerTest::makeTestSubject(),
     which is how get isolation within test methods.
    */
    private static array $_records = [];
    private static array $_recordsByLevel = [];
    public static function resetRecords(): void
    {
        self::$_records = [];
        self::$_recordsByLevel = [];
    }


    public function __construct()
    {
        $this->linkRecordStorage();
    }

    public function __clone()
    {
        $this->linkRecordStorage();
    }

    private function linkRecordStorage(): void
    {
        $this->records = &self::$_records;
        $this->recordsByLevel = &self::$_recordsByLevel;
    }
}
