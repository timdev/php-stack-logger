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
    private ExtendedMonologLogger $log;

    protected function makeTestSubject(): ExtendedMonologLogger
    {
        return $this->log = new ExtendedMonologLogger();
    }

    public function testWithnameCloning(): void
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

        // ensure no records were written with the original channel
        $this->assertCount(0, $log->getChannelRecords());
        // and one was written to the new channel
        $this->assertCount(1, $newChannel->getChannelRecords());

        /*
        This is mostly to prove that static analysis knows $newChannel is an ExtendedMonologLogger, even though
        `StackMonologLoggerTrait::withName(): \Monolog\Logger` tells us it isn't.
        */
        $this->assertTrue($newChannel->extraMethod());
    }

    public function testWithnameClonesTrackParentContext(): void
    {

        $original = $this->log->withContext(['original' => 'context']);

        // withName()'d clone includes parent's context.
        $renamed = $original->withName('new');
        $this->assertCount(1, $original->getContext());
        $this->assertCount(1, $renamed->getContext());

        // context added to the original (post-cloning) ...
        $original->addContext(['additional' => 'context added to original logger']);
        // ... is also visible in child ...
        $this->assertEquals(2, $renamed->countContext());
        // .. and in any logged messages
        $renamed->info('I should have two context items');
        $this->assertEquals(2, $renamed->contextCountAt(0));
    }

    public function testWithnameConvoluted(): void
    {
        /*
         This test is about doing a complex set of withName/withContext/addContext
         in non-naive order. It's not based on any observed use-case, but is more
         of a general sanity-check.
        */

        $first = $this->makeTestSubject()->withName('first');
        $second = $first->withName('second');
        $second = $second->withContext(['second' => 'context']);

        $first->addContext(['first' => 'context']);
        $third = $second->withContext(['third' => 'context'])->withName('third');

        $second->info('Second should have 2 context');
        $this->assertEquals(2, $second->contextCountAt(0));
        $this->assertCount(2, $second->getContext());

        $this->assertCount(3, $third->getContext());
        $this->assertCount(1, $first->getContext());

        $first->addContext(['first2' => 'second context for first logger']);
        $this->assertCount(3, $second->getContext());
        $this->assertCount(4, $third->getContext());

        $first->debug('First now has one record with two context');
        $this->assertCount(1, $first->getChannelRecords());
        $this->assertCount(2, $first->getChannelRecords()[0]['context']);

        $this->assertEquals(2, $first->contextCountAt(0));
    }
}
