<?php
declare(strict_types=1);

namespace TimDev\StackLogger;

use Psr\Log\NullLogger as Psr3NullLogger;

/**
 *
 * @codeCoverageIgnore
 */
class NullLogger extends Psr3NullLogger implements LoggerInterface
{
    public function withContext(array $context = []): static
    {
        return $this;
    }

    public function addContext(array $context): static
    {
        return $this;
    }

    public function withName(string $name): static
    {
        return $this;
    }
}
