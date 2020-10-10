<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use PHPUnit\Framework\TestCase;

final class TraitTest extends TestCase
{
    public function testBasicChildFunctionality()
    {
        $logger = new TraitTestLogger();
        $child = $logger->child(['some_context' => 'on child']);
        $grandchild = $child->child(['grandchild' => 'context here']);
        
        
        $child->info('Child message');
        $grandchild->warning('Grandchild message');
        $this->assertCount(1, $logger->records[0]['context']);
        $this->assertCount(2, $logger->records[1]['context']);
        
        $this->assertEquals(
            ['some_context', 'grandchild'], 
            array_keys($logger->records[1]['context'])
        );
    }
}
