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
        // merge any passed context on top of my own
        $context = array_replace($this->context, $context);
        
        // recursively accumulate context up the chain of ancestry
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

    /**
     * This method from Monolog is special because it returns a clone.
     * 
     * Note: the return type hint must be \Monolog\Logger to stay compatible with the parent method's signature. Oddly,
     * if we were not using a trait here, we could specify the return type as `self`, but PHP won't allow us to do that
     * from a trait for some reason. 
     * 
     * To deal with it, we explicitly (and imprecisely) hint \Monolog\Logger, and add a phpDoc directives specifying
     * `static`. This manages to satisfy both the PHP runtime and my (Jetbrains) IDE. In a PHP8 future, once Monolog
     * updates the return type of withName() to be static, we can use static here as well.
     * 
     * @param string $name
     * @return static
     */
    public function withName(string $name): \Monolog\Logger
    {
        $new = parent::withName($name);
        $new->parent = null;
        $new->context = $this->parent 
            ? array_replace($this->parent->context, $this->context)
            : $this->context;
        return $new;
    }
    
}
