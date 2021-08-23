<?php

declare(strict_types=1);

namespace TimDev\StackLogger;

use Monolog\Handler\NullHandler;
use Monolog\Logger as MonologLogger;

/**
 * Extends Psr3Logger to provide a monolog-like withName() method.
 *
 * @extends Psr3Logger<MonologLogger>
 */
class Monolog extends Psr3Logger
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

    public static function getNullLogger(): self
    {
        return new self(new MonologLogger('null', [new NullHandler()]));
    }
}
