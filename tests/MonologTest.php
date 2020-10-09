<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use Monolog\Handler\TestHandler;
use Monolog\Logger as MonologLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\Logger;

final class MonologTest extends TestCase
{
    private $monolog;
    private $handler;
    
    public function setUp(): void
    {
        $this->monolog = new MonologLogger('test');
        $this->handler = new TestHandler();
        $this->monolog->pushHandler($this->handler);
    }
    
    public function testCanHandleWithNameCalls()
    {
        $logger = new Logger($this->monolog);
        $child = $logger->child(['basic' => 'context']);
        
        
        /** @var Logger $newChannel */
        $newChannel = $child->withName('other');
        $newChannel->info('A message');
        
        $rec = $this->handler->getRecords()[0];
        $this->assertEquals('other', $rec['channel']);
        $this->assertCount(1, $rec['context']);
    }
    
    
    
}
