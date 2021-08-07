<?php

declare(strict_types=1);

namespace TimDev\StackLogger;

use Psr\Log\LoggerInterface as PSR3Logger;
use Psr\Log\LogLevel;

/**
 * @template L
 */
class WrapLogger implements LoggerInterface
{
    /** @var L */
    private PSR3Logger $logger;

    /** @psalm-var static  */
    protected ?LoggerInterface $parent = null;

    protected array $context = [];

    /**
     * @param L $logger
     */
    public function __construct(PSR3Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return L
     */
    public function getWrapped(): PSR3Logger
    {
        return $this->logger;
    }

    /** Context Handling */
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
        $pc = $this->parent
            ? $this->parent->mergedContext()
            : [];
        return array_merge($pc, $this->context);
    }

    /* PSR3 Implementation, wrapping our wrapped instance. */

    /**
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = []): void
    {
        assert(is_string($level) || is_int($level));
        // merge passed context on top of accumulated context,
        // and process callable context elements.
        $context = $this->processContext($context);

        // actually log the message.
        $this->logger->log($level, $message, $context);
    }


    public function emergency($message, array $context = [])
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




}
