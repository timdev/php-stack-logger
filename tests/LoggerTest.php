<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    /**
     * The real/main/top/root logger. Since we're using a TestLogger, this is where records are accumulated.
     * 
     * @var ExtendedTestLogger 
     */
    private $log;
    
    public function setUp(): void
    {
        $this->log = new ExtendedTestLogger();
    }
    
    public function testLogsMessages()
    {
        $this->log->info('Log me some message');
        $this->log->warning('Log me some warning.');
        $this->assertTrue($this->log->hasInfoThatContains('Log me'));
        $this->assertTrue($this->log->hasWarningThatContains('warning.'));
    }
    
    public function testCanCreateChild()
    {
        $log = $this->log->child(['initial' => 'context']);
        $log->debug('I should have some context from my constructor arg');
        $this->assertEquals('context', $this->log->records[0]['context']['initial']);
    }
    
    public function testAccumulatesContext()
    {
        $log = $this->log->child(['initial' => 'context']);
        $child = $log->child(['more' => 'context']);
        $child->warning('I should have three context items', ['final' => 'context']);
        $this->assertCount(3, $this->log->contextAt(0));
        $this->assertEquals(
            ['initial', 'more', 'final'],
            $this->log->contextKeysAt(0)
        );
    }
    
    public function textMergesContext()
    {
        $log = $this->log->child(['a' => 'Alice']);
        $log
            ->child(['a' => 'Allison', 'b' => 'Bob'])
            ->info('Should be Allison and Bruno', ['b' => 'Bruno']);
        $this->assertEquals(
            ['Allison', 'Bruno'], 
            $this->log->contextValuesAt(0)
        );
    }
    
    public function testInvokesCallables()
    {
        $logger = $this->log;
        $child = $logger->child([
            'counter' => function(array $ctx){
                return count($ctx);
            }
        ]);
        $child->notice('No context added to this message');
        $this->assertEquals(1, $this->log->contextAt(0)['counter']);
        
        $child = $child->child(['Second' => 'Context Item']);
        $child->warning('A log with three context items.', ['Third' => 'Context Item']);
        $this->assertEquals(
            $this->log->contextCountAt(1),     // === 3  
            $this->log->records[1]['context']['counter']
        );
    }
}
