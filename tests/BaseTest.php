<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use PHPUnit\Framework\TestCase;
use TimDev\StackLogger\Test\Support\TestLoggerInterface;

/**
 * Base class for testing against various loggers.
 *
 * We want to run these tests against extensions of various logger implementations. Actual test classes extend this
 * and implement makeTestSubject().
 */
abstract class BaseTest extends TestCase
{
    protected TestLoggerInterface $log;

    abstract protected function makeTestSubject(): TestLoggerInterface;

    public function setUp(): void
    {
        $this->log = $this->makeTestSubject();
    }

    public function testLogsMessages()
    {
        $this->log->info('Log me some message');
        $this->log->warning('Log me some warning.');
        $records = $this->log->getRecords();

        $this->assertEquals('Log me some message', $records[0]['message']);
        $this->assertEquals('Log me some warning.', $records[1]['message']);
    }

    public function testCanCreateChild()
    {
        $log = $this->log->child(['initial' => 'context']);
        $log->debug('I should have some context from my constructor arg');
        $this->assertEquals('context', $this->log->recordAt(0)['context']['initial']);
    }

    public function testAccumulatesContext()
    {
        $log = $this->log->child(['initial' => 'context']);
        $child = $log->child(['more' => 'context']);

        // $child should have two context items.
        $child->warning('I should have three context items', ['final' => 'context']);
        $this->assertCount(3, $this->log->contextAt(0));
        $this->assertEquals(
            ['initial', 'more', 'final'],
            $this->log->contextKeysAt(0)
        );

        // Messages logged by $log should not anything beyond the 'initial' context item.
        $log->notice('I should have one context item');
        $this->assertCount(1, $log->contextAt(1));
        $this->assertEquals(
            ['initial'],
            $this->log->contextKeysAt(1)
        );
    }

    public function textMergesContext()
    {
        $log = $this->log->child(['a' => 'Alice']);

        // Alice should be overwritten with Allison in child.
        $log
            ->child(['a' => 'Allison', 'b' => 'Bob'])
            ->info('Allison and Bruno', ['b' => 'Bruno']);
        $this->assertEquals(
            ['Allison', 'Bruno'],
            $this->log->contextValuesAt(0)
        );

        // But original logger should still have (only) Alice.
        $log->info('Alice alone.');
        $this->assertEquals(
            ['a' => 'Alice'],
            $this->log->contextAt(0)
        );
    }

    public function testInvokesCallables()
    {
        $logger = $this->log;
        $child = $logger->child(
            [
                // A callable context element that returns the number of
                // elements in the record's context.
                'counter' => function(array $ctx) {
                    return count($ctx);
                }
            ]
        );
        $child->notice('Only one context element, the result of the callable.');
        $this->assertEquals(1, $this->log->contextAt(0)['counter']);

        $child = $child->child(['Second' => 'Context Item']);
        $child->warning('A log with three context items.', ['Third' => 'Context Item']);
        $this->assertEquals(
            $this->log->contextCountAt(1),          // === 3  
            $this->log->contextAt(1)['counter']
        );
    }
}
