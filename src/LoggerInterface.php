<?php declare(strict_types=1);

namespace TimDev\StackLogger;

interface LoggerInterface extends \Psr\Log\LoggerInterface
{
    /**
     * Returns a
     */
    public function child(array $context = []): self;
}
