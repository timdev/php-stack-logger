<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use Monolog\Handler\TestHandler;
use PHPUnit\Framework\TestCase;

final class MonologTest extends TestCase
{
    private $log;
    private $handler;
    
    public function setUp(): void
    {
        $this->log = new ExtendedMonologLogger('test');
        $this->handler = new TestHandler();
        $this->log->pushHandler($this->handler);
    }
        
    public function testCanHandleWithNameCalls()
    {
        // push some context. 
        $log = $this->log->child(['basic' => 'context']);
        
        // get a clone with a new monolog channel-name, and log to it.
        $newChannel = $log->withName('other');
        $newChannel->info('A message');
        
        // ensure the handler has accumulated records with context attached.
        $rec = $this->handler->getRecords()[0];
        $this->assertEquals('other', $rec['channel']);
        $this->assertCount(1, $rec['context']);
        $this->assertEquals('context', $rec['context']['basic']);
        
        /* 
        This is mostly to prove that static analysis knows $newChannel is an ExtendedMonologLogger, even though 
        `StackMonologLoggerTrait::withName(): \Monolog\Logger` tells us it isn't.          
        */         
        $this->assertTrue($newChannel->extraMethod());
    }
}
