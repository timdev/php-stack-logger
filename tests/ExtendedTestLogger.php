<?php
/** @noinspection ReturnTypeCanBeDeclaredInspection */
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use Psr\Log\Test\TestLogger;

final class ExtendedTestLogger extends TestLogger
{
    private $name = 'default';
    public function getName(): string { return $this->name; }
    
    public function toUpper(string $str)
    {
        return strtoupper($str);
    }

    /**
     * This is basically what Monolog\Logger::withName() does.   
     */
    public function withName(string $name): ExtendedTestLogger
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }
}
