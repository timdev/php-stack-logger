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
    public function withName(string $name): static
    {
        $new = new static($this->getWrapped()->withName($name));
        $new->parent = $this;
        return $new;
    }

}
