<?php

declare(strict_types=1);

namespace TimDev\StackLogger;

use Psr\Log\LoggerInterface as PsrInterface;
use Psr\Log\LogLevel;

/**
 * Implements LoggerInterface by wrapping a PSR3 logger.
 *
 * @template L of PsrInterface
 */
class Psr3StackLogger implements StackLogger
{
    /** @var L */
    protected PsrInterface $logger;

    /** @psalm-var static  */
    protected ?StackLogger $parent = null;

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

    /** @param L $logger */
    final protected function setWrapped(PsrInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Context Handling
     *
     * @param array $context
     * @return static<L>
     */
    public function withContext(array $context = []): static
    {
        $child = clone $this;
        $child->parent = $this;
        $child->context = $context;
        return $child;
    }

    public function addContext(array $context): static
    {
        $this->context = array_merge($this->context, $context);
        return $this;
    }

    /**
     * Merges $context on top of the instances accumulated context, and
     * processes any callable elements in the final context.
     */
    protected function processContext(array $context = []): array
    {
        $context = array_merge($this->mergedContext(), $context);

        // handle any callables in final context.
        return array_map(
            static fn($c): mixed => is_callable($c) ? $c($context) : $c,
            $context
        );
    }

    protected function mergedContext(): array
    {
        /*
         @todo since this code can be called a *ton* in every request, this
               recursive array_merge approach may be seriously sub-optimal:
               https://github.com/kalessil/phpinspectionsea/blob/master/docs/performance.md#slow-array-function-used-in-loop

               An easy win: collect ancestor-context arrays and do a single
               merge at the end.
               Bigger win: do the merging in withContext(), but I feel like I
               investigated that once, and there was a reason not to do that.

        */
        $pc = $this->parent
            ? $this->parent->mergedContext()
            : [];
        return array_merge($pc, $this->context);
    }

    /* PSR3 Implementation, wrapping our wrapped instance. */

    public function log($level, $message, array $context = []): void
    {
        assert(is_string($level) || is_int($level));
        $context = $this->processContext($context);
        $this->logger->log($level, $message, $context);
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = [])
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = [])
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = [])
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = [])
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = [])
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = [])
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = [])
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public static function getNullLogger(): self
    {
        return new self(new \Psr\Log\NullLogger());
    }
}
