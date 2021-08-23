<?php

declare(strict_types=1);

namespace TimDev\StackLogger;

interface LoggerInterface extends \Psr\Log\LoggerInterface
{
    /**
     * Returns a new instance that has $context merged on top of any existing
     * context.
     *
     * Useful when you're passing a logger up the call stack by passing or
     * receiving the logger up or down the call stack.
     */
    public function withContext(array $context = []): static;

    /**
     * Merge $context onto this instance's context.
     *
     * Useful in contexts like HTTP middleware, where you want to add
     * some global context onto the logger, but aren't passing the a
     * logger onwards as an argument.
     */
    public function addContext(array $context): static;

    public static function getNullLogger(): self;
}
