<?php
declare(strict_types=1);

namespace TimDev\StackLogger;

trait StackLoggerTrait
{
    protected ?LoggerInterface $parent = null;

    protected array $context = [];

    /**
     * {@inheritDoc}
     *
     * @return static
     */
    public function withContext(array $context = []): self
    {
        $child = clone $this;
        $child->parent = $this;
        $child->context = $context;
        return $child;
    }

    /**
     * {@inheritDoc}
     */
    public function addContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);
        return $this;
    }

    /**
     * Many (but crucially, not all, you need to check!) PSR3 loggers use
     * `log()` as the fundamental method for logging messages. In those cases,
     * we can simply intercept log() calls and do our magic.
     */
    public function log($level, $message, array $context = []): void
    {
        // merge passed context on top of accumulated context,
        // and process callable context elements.
        $context = $this->processContext($context);

        // actually log the message.
        parent::log($level, $message, $context);
    }

    /**
     * Merges $context on top of the instances accumulated context, and
     * processes any callable elements in the final context. Factored out from
     * log() above, since it's occasionally usefule elsewhere (like in
     * `MonologStackLoggerTrait::addRecord()`.
     */
    protected function processContext(array $context = []): array
    {
        $context = array_merge($this->mergedContext(), $context);

        // handle any callables in final context.
        $context = array_map(
            static fn($c) => is_callable($c) ? $c($context) : $c,
            $context
        );
        return $context;
    }

    protected function mergedContext(): array
    {
        $pc = $this->parent
            ? $this->parent->mergedContext()
            : [];
        return array_merge($pc, $this->context);
    }
}
