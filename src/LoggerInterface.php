<?php declare(strict_types=1);

namespace TimDev\StackLogger;

interface LoggerInterface extends \Psr\Log\LoggerInterface
{
    /**
     * Returns a new instance that has $context merged on top of any existing context.
     */
    public function child(array $context = []): self;
}
