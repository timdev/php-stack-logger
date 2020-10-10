<?php
declare(strict_types=1);

namespace TimDev\StackLogger;

trait StackLoggerTrait
{
    /** @var array */
    private $context = [];
    
    /** @var self|null */
    private $parent = null;

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = []): void
    {
        // recursively accumulate context up the chain of ancestry
        $context = array_replace($this->context, $context);
        if ($this->parent){
            $this->parent->log($level, $message, $context);
            return; 
        }
        
        // base case: handle callables and call superclass log().
        $context = array_map(function($c) use ($context) {
            return is_callable($c) ? $c($context) : $c;
        }, $context);
        parent::log($level, $message, $context);
    }

    /**
     * Returns a child logger with additional tracked context.
     */
    public function child(array $context = []): self
    {
        $child = clone $this;
        $child->context = $context;
        $child->parent = $this;
        return $child;
    }
    
}
