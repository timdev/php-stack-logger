<?php declare(strict_types=1);

namespace TimDev\StackLogger;

use Psr\Log\LoggerInterface as PsrInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\NullLogger;

/**
 * Implements LoggerInterface by wrapping a PSR3 logger.
 *
 * @phpstan-template L of PsrInterface
 */
class Psr3StackLogger implements StackLogger
{
    use LoggerTrait;

    /** @var L */
    protected PsrInterface $logger;

    /** @var static */
    protected ?StackLogger $parent = null;

    /** @var array<mixed> */
    protected array $context = [];

    /** @param L $logger */
    public function __construct(PsrInterface $logger)
    {
        $this->setWrapped($logger);
    }

    /** @return L */
    public function getWrapped(): PsrInterface
    {
        return $this->logger;
    }

    /**
     * Context Handling
     *
     * @param array<string, mixed> $context
     */
    #[\Override]
    public function withContext(array $context = []): static
    {
        $child          = clone $this;
        $child->parent  = $this;
        $child->context = $context;
        return $child;
    }

    /**
     * @param array<string, mixed> $context
     * @return $this
     */
    #[\Override]
    public function addContext(array $context): static
    {
        $this->context = array_merge($this->context, $context);
        return $this;
    }

    /* PSR3 Implementation, wrapping our wrapped instance. */

    #[\Override]
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        assert(is_string($level) || is_int($level));
        $context = $this->processContext($context);
        $this->logger->log($level, $message, $context);
    }

    public static function makeNullLogger(): StackLogger
    {
        $instance = new NullLogger();
        return new self($instance);
    }

    /** @param L $logger */
    final protected function setWrapped(PsrInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Merges $context on top of the instances accumulated context, and
     * processes any callable elements in the final context.
     *
     * @param array<mixed> $context
     * @return array<mixed>
     */
    protected function processContext(array $context = []): array
    {
        $context = array_merge($this->mergedContext(), $context);

        // handle any callables in final context.
        return array_map(static fn($c): mixed => is_callable($c) ? $c($context) : $c, $context);
    }

    /**
     * Returns an array like [grandparentContext, parentContext, myContext]
     * @return list<array>
     */
    protected function contextToMerge(): array
    {
        return [...($this->parent !== null) ? $this->parent->contextToMerge() : [], $this->context];
    }

    /** @return array<mixed> */
    protected function mergedContext(): array
    {
        return array_merge(...$this->contextToMerge());
    }
}
