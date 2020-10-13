<?php
declare(strict_types=1);

namespace TimDev\StackLogger;


trait StackLoggerTrait
{    

    protected array $context = [];

    /**
     * Many (but crucially, not all, you need to check!) PSR3 loggers use `log()` as the fundamental method for logging
     * messages. In those cases, we can 
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
     * Returns a child logger with additional tracked context.
     */
    public function child(array $context = []): self
    {
        $child = clone $this;
        $child->context = array_merge($this->context, $context);
        return $child;
    }

    /**
     * Merges $context on top of the instances accumulated context, and processes any callable elements in the final
     * context. Factored out from log() above, since it's occasionally usefule elsewhere (like in
     * `MonologStackLoggerTrait::addRecord()`.
     */
    protected function processContext(array $context): array
    {
        // get final context
        $context = array_merge($this->context, $context);

        // handle any callables in final context.
        $context = array_map(function($c) use ($context) {
            return is_callable($c) ? $c($context) : $c;
        }, $context);
        
        return $context;
    }

    
}
