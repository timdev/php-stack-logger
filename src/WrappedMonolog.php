<?php

declare(strict_types=1);

namespace TimDev\StackLogger;

use Monolog\Logger as MonologLogger;

/**
 * Extends WrappedPSR3 to provide a monolog-like withName() method.
 *
 * @extends WrappedPSR3<MonologLogger>
 */
class WrappedMonolog extends WrappedPSR3
{
    public function __construct(MonologLogger $logger)
    {
        parent::__construct($logger);
    }

    public function withName(string $name): static
    {
        // this works, but requires WrappedPSR3::$logger to be non-private.
        // Is there a better way?
        $new = clone $this;
        $new->logger = $this->logger->withName($name);
        $new->parent = $this;
        return $new;
    }
}
