<?php declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use TimDev\StackLogger\Test\Support\MonologStackLogger;

/**
 * Test against monolog.
 *
 * WrappedMonolog supports monolog's withName() method, so we have some extra
 * cases here to test that support.
 */
class MonologTest extends BaseTestCase
{
    public function testWithnameCloning(): void
    {
        // push some context.
        $log = $this->makeTestSubject()
            ->withContext(['basic' => 'context']);

        // get a clone with a new monolog channel-name, and log to it.
        $newChannel = $log->withName('other');
        $newChannel->info('A message');

        // ensure the handler has accumulated records with context attached.
        $rec = $log->recordAt(0);
        self::assertEquals('other', $rec['channel']);
        self::assertIsArray($rec['context']);
        self::assertCount(1, $rec['context']);
        self::assertEquals('context', $rec['context']['basic']);

        // ensure no records were written with the original channel
        self::assertCount(0, $log->getChannelRecords());
        // and one was written to the new channel
        self::assertCount(1, $newChannel->getChannelRecords());

        /*
        This is mostly to prove that static analysis knows $newChannel is an ExtendedMonologLogger, even though
        `StackMonologLoggerTrait::withName(): \Monolog\Logger` tells us it isn't.
        */
        self::assertTrue($newChannel->extraMethod());
    }

    public function testWithnameClonesTrackParentContext(): void
    {
        $original = $this->makeTestSubject()
            ->withContext(['original' => 'context']);

        // withName()'d clone includes parent's context.
        $renamed = $original->withName('new');
        self::assertCount(1, $original->getContext());
        self::assertCount(1, $renamed->getContext());

        // context added to the original (post-cloning) ...
        $original->addContext(['additional' => 'context added to original logger']);
        // ... is also visible in child ...
        self::assertEquals(2, $renamed->countContext());
        // .. and in any logged messages
        $renamed->info('I should have two context items');
        self::assertEquals(2, $renamed->contextCountAt(0));
    }

    public function testWithnameConvoluted(): void
    {
        /*
         This test is about doing a complex set of withName/withContext/addContext
         in non-naive order. It's not based on any observed use-case, but is more
         of a general sanity-check.
        */

        $first = $this->makeTestSubject()
            ->withName('first');
        $second = $first->withName('second');
        $second = $second->withContext(['second' => 'context']);

        $first->addContext(['first' => 'context']);
        $third = $second->withContext(['third' => 'context'])->withName('third');

        $second->info('Second should have 2 context');
        self::assertEquals(2, $second->contextCountAt(0));
        self::assertCount(2, $second->getContext());

        self::assertCount(3, $third->getContext());
        self::assertCount(1, $first->getContext());

        $first->addContext(['first2' => 'second context for first logger']);
        self::assertCount(3, $second->getContext());
        self::assertCount(4, $third->getContext());

        $first->debug('First now has one record with two context');
        self::assertCount(1, $first->getChannelRecords());
        self::assertIsIterable($first->channelRecordAt(0)['context']);
        self::assertCount(2, $first->channelRecordAt(0)['context']);
        self::assertEquals(2, $first->contextCountAt(0));
    }

    public function testNullLoggerFactory(): void
    {
        $null = MonologStackLogger::makeNullLogger();
        self::assertInstanceOf(\TimDev\StackLogger\MonologStackLogger::class, $null);
    }

    protected function makeTestSubject(): MonologStackLogger
    {
        return new MonologStackLogger();
    }
}
