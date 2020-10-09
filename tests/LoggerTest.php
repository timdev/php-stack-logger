<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\Logger;

final class LoggerTest extends TestCase
{
    private $testLogger;
    
    public function setUp(): void
    {
        $this->testLogger = new TestLogger();
    }
    
    public function testCanWrapLogger()
    {
        $logger = new Logger($this->testLogger);
        $logger->info('Log me some message');
        $this->assertTrue($this->testLogger->hasInfoThatContains('Log me'));
    }
    
    public function testAcceptsContextInConstructor()
    {
        $logger = new Logger($this->testLogger, ['initial' => 'context']);
        $logger->debug('I should have some context from my constructor arg');
        $this->assertEquals('context', $this->testLogger->records[0]['context']['initial']);
    }
    
    public function testCorrectlyAccumulatesContext()
    {
        $logger = new Logger($this->testLogger, ['initial' => 'context']);
        $child = $logger->child(['more' => 'context']);
        $child->warning('I should have three context items', ['final' => 'context']);
        $this->assertCount(3, $this->testLogger->records[0]['context']);
        $this->assertEquals(
            ['initial', 'more', 'final'], 
            array_keys($this->testLogger->records[0]['context'])
        );
    }
    
    public function testCorrectlyMergesContext()
    {
        $logger = new Logger($this->testLogger, ['a' => 'Alice']);
        $logger
            ->child(['a' => 'Allison', 'b' => 'Bob'])
            ->info('Should be Allison and Bruno', ['b' => 'Bruno']);
        $this->assertEquals(
            ['Allison', 'Bruno'], 
            array_values($this->testLogger->records[0]['context'])
        );
    }
    
    public function testInvokesCallables()
    {
        $logger = new Logger($this->testLogger);
        $child = $logger->child([
            'counter' => function(array $ctx){
                return count($ctx);
            }
        ]);
        $child->notice('No context added to this message');
        $this->assertEquals(1, $this->testLogger->records[0]['context']['counter']);
        
        $child = $child->child(['Second' => 'Context Item']);
        $child->warning('A log with three context items.', ['Third' => 'Context Item']);
        $this->assertEquals(
            count($this->testLogger->records[1]['context']),     // === 3
            $this->testLogger->records[1]['context']['counter']
        );
    }
    
    public function testProxiesSimpleMethodCalls()
    {
        $logger = new Logger(new ExtendedTestLogger());
        $this->assertEquals('FOO', $logger->toUpper('foo'));
    }
    
    public function testWrapsLoggerInterfaceProxyReturnValues()
    {
        $logger = new Logger(new ExtendedTestLogger());
        $renamed = $logger->withName('CustomName');
        $this->assertInstanceOf(Logger::class, $renamed);
        $this->assertEquals('CustomName', $renamed->getName());
    }
    
    public function testLogsWarningOnInvalidMethodProxy()
    {
        
        $logger = new Logger($this->testLogger);
        $fail = $logger->methodDoesNotExist();
        $this->assertFalse($fail);
        $this->assertTrue($this->testLogger->hasWarningThatContains("logger doesn't know how to"));
    }
}
