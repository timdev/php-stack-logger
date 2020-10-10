<?php
declare(strict_types=1);

namespace TimDev\StackLogger;

/**
 * If you're extending a Monolog\Logger, and your code calls Monolog\Logger::addRecord() directly,
 * you can use this trait instead of StackLoggerTrait.
 */
trait StackMonologLoggerTrait 
{
    use StackLoggerTrait;

    /* Annoying monolog-compatibility boilerplate, since monolog predates PSR-3, and is
       also the dominant PHP logging library. Maybe move these to a monolog-specific trait? */

    public function addRecord(int $level, string $message, array $context = []): bool
    {
        // recursively accumulate context up the chain of ancestry
        $context = array_replace($this->context, $context);
        if ($this->parent){
            $this->parent->addRecord($level, $message, $context);
            return true;
        }

        // base case: handle callables and call superclass log().
        $context = array_map(function($c) use ($context) {
            return is_callable($c) ? $c($context) : $c;
        }, $context);
        return parent::addRecord($level, $message, $context);
    }
    
}
