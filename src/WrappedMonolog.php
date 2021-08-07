<?php

declare(strict_types=1);

namespace TimDev\StackLogger;

/**
 * Extends WrappedPSR3 to provide a monolog-like withName() method.
 *
 * @template T
 * @extends WrappedPSR3<T>
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
