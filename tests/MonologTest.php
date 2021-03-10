<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\ExtendedMonologLogger;

/**
 * Test against monolog.
 *
 * Uses ExtendedMonologLogger as a subject, with additional test method(s) for
 * monolog-specific stuff.
 */
class MonologTest extends BaseTest
{

    protected function makeTestSubject(): ExtendedMonologLogger
    {
        return new ExtendedMonologLogger();
    }

    public function test_withName_cloning()
    {
        // push some context.
        $log = $this->makeTestSubject()->withContext(['basic' => 'context']);

        // get a clone with a new monolog channel-name, and log to it.
        $newChannel = $log->withName('other');
        $newChannel->info('A message');

        // ensure the handler has accumulated records with context attached.
        $rec = $log->recordAt(0);

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
