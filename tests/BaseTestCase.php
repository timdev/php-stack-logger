<?php

declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use PHPUnit\Framework\TestCase;
use TimDev\StackLogger\StackLogger;
use TimDev\StackLogger\Test\Support\TestStackLogger;

/**
 * Base class for testing against various loggers.
 *
 * We want to run these tests against extensions of various logger
 * implementations. Actual test classes extend this and implement
 * makeTestSubject().
 */
abstract class BaseTestCase extends TestCase
{
    abstract protected function makeTestSubject(): TestStackLogger;

    private TestStackLogger $log;

    public function setUp(): void
    {
        $this->log = $this->makeTestSubject();
    }

    public function testLogsMessages(): void
    {
        $this->log->info('Log me some message');
        $this->log->warning('Log me some warning.');
        $records = $this->log->getRecords();

        $this->assertEquals('Log me some message', $records[0]['message']);
        $this->assertEquals('Log me some warning.', $records[1]['message']);
    }

    public function testAddsContext(): void
    {
        $this->log->addContext(['some' => 'context'])->info('An info.');
        $this->assertEquals(['some'], $this->log->contextKeysAt(0));

        $this->log
            ->addContext(['more' => 'context'])
            ->info('I should have two bits of context.');
        $this->assertEquals(2, $this->log->contextCountAt(1));
        $this->assertEquals(['some', 'more'], $this->log->contextKeysAt(1));

        $this->log
            ->addContext(['even more' => 'context'])
            ->warning(
                'This message should get four context elements.',
                ['foo' => 'bar']
            );
        $this->assertEquals(4, $this->log->contextCountAt(2));

        $this->log->debug('Back to three!');
        $this->assertEquals(['some', 'more', 'even more'], $this->log->contextKeysAt(3));
    }

    public function testCreateChildWithContext(): void
    {
        $log = $this->log->withContext(['initial' => 'context']);
        $log->debug('I should have some context from my constructor arg');
        $this->assertEquals('context', $log->recordAt(0)['context']['initial']);
    }

    public function testAccumulatesContext(): void
    {
        $log = $this->log->withContext(['initial' => 'context']);
        $child = $log->withContext(['more' => 'context']);

        // $child should have two context items.
        $child->warning('I should have three context items', ['final' => 'context']);
        $this->assertCount(3, $child->contextAt(0));

        // Keys should be identical. Order is not guaranteed
        $contextKeys = $child->contextKeysAt(0);
        foreach (['initial', 'more', 'final'] as $key) {
            $this->assertContains($key, $contextKeys);
        }

        // Messages logged by $log should not anything beyond the 'initial' context item.
        $log->info('I should have one context item (on my second record)');

        $this->assertCount(1, $log->contextAt(1));
        $this->assertEquals(
            ['initial'],
            $log->contextKeysAt(1)
        );
    }

    public function testMergesContext(): void
    {
        $log = $this->log->withContext(['a' => 'Alice']);

        // Alice should be overwritten with Allison in child.
        $log
            ->withContext(['a' => 'Allison', 'b' => 'Bob'])
            ->emergency('Allison and Bruno', ['b' => 'Bruno']);
        $this->assertEquals(
            ['Allison', 'Bruno'],
            $this->log->contextValuesAt(0)
        );

        // But original logger should still have (only) Alice.
        $log->critical('Alice alone.');
        $this->assertEquals(
            ['a' => 'Alice'],
            $this->log->contextAt(1)
        );
    }

    public function testInvokesCallables(): void
    {
        $logger = $this->log;
        $child = $logger->withContext(
            [
                // A callable context element that returns the number of
                // elements in the record's context.
                'counter' => function (array $ctx) {
                    return count($ctx);
                }
            ]
        );
        $child->notice('Only one context element, the result of the callable.');
        $this->assertEquals(1, $child->contextAt(0)['counter']);

        $child = $child->withContext(['Second' => 'Context Item']);
        $child->warning('A log with three context items.', ['Third' => 'Context Item']);
        $this->assertEquals(
            $child->contextCountAt(1),          // === 3
            $child->contextAt(1)['counter']
        );

        $start = microtime(true);
        $child2 = $logger->withContext(['elapsed_micros' => fn() => 1000000 * (microtime(true) - $start)]);
        usleep(1000);
        $child2->alert('At least 1000 μ-sec have passed.');
        $this->assertGreaterThan(1000, $child2->contextAt(2)['elapsed_micros']);
    }

    /*
     * None of the other tests go beyond approximately 3 loggers in a chain. This test
     * is a sanity check for all the core features with a long chain of loggers.
     */
    public function testLongChain(): void
    {
        $logger = $this->log;
        $numLoggers = 20;
        foreach (range(1, $numLoggers) as $i) {
            $logger = $logger->withContext(["gen{$i}" => $i]);
        }
        $logger->withContext(["count" => fn(array $ctx) => count($ctx)]);
        $logger->error("I come from a long lineage", ['final' => 'I should be the 21st context element']);
        $this->assertCount($numLoggers + 1, $logger->recordAt(0)['context']);
    }

    public function testNullLoggerFactory(): void
    {
        $null = $this->log::makeNullLogger();
        $this->assertInstanceOf(StackLogger::class, $null);
    }
}
