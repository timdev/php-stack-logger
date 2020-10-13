<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use Monolog\Handler\TestHandler;
use TimDev\StackLogger\Test\Support\ExtendedMonologLogger;
use TimDev\StackLogger\Test\BaseTest;
use TimDev\StackLogger\Test\Support\TestLoggerInterface;

/**
 * Test against monolog.
 * 
 * Uses ExtendedMonologLogger as a subject, with additional test method(s) for monolog-specific stuff.
 */
class MonologTest extends BaseTest
{
    /* PHP 7.4 doesn't support covariant typed properties 😢 */
    /** @var ExtendedMonologLogger  */
    protected TestLoggerInterface $log;
    
    protected function makeTestSubject(): ExtendedMonologLogger
    {
        return  new ExtendedMonologLogger();
    }
        
    public function test_withName_cloning()
    {
        // push some context. 
        $log = $this->log->child(['basic' => 'context']);
        
        // get a clone with a new monolog channel-name, and log to it.
        $newChannel = $log->withName('other');
        $newChannel->info('A message');
        
        // ensure the handler has accumulated records with context attached.
        $rec = $this->log->recordAt(0);
        
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
