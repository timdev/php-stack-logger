<?php declare(strict_types=1);

namespace TimDev\StackLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class Logger extends AbstractLogger
{
    private $wrapped;
    private $context;
    private $parent;

    /**
     * @param LoggerInterface $wrapped Messages are delegated to this logger.
     * @param array           $context Context added to messages logged by this logger and any children.
     */
    public function __construct(LoggerInterface $wrapped, array $context = [])
    {
        $this->wrapped = $wrapped;
        $this->context = $context;
    }

    /**
     * {@inheritdoc}     
     */
    public function log($level, $message, array $context = []): void
    {
        $context = array_replace($this->context, $context);
        $context = array_map(function($c) use ($context) {
            return is_callable($c) ? $c($context) : $c;
        }, $context);
        $this->wrapped->log($level, $message, $context);
    }

    /**
     * Returns a child logger with additional tracked context.
     */
    public function child(array $context = []): Logger
    {
        $child = new static($this->wrapped, array_replace($this->context, $context));
        $child->parent = $this;
        return $child;
    }

    /**
     * This may go away, not clear it's a good idea.
     * 
     * Some PSR-3 implementations might have additional methods. In particular, Monolog's Logger has a `withName($name)`
     * method that returns a clone with a different channel-name. That's an even trickier case, since simply proxying
     * the method means we'll end up with a MonologLogger, instead of a StackLogger. So at the end of this magic method,
     * we check the return value of the proxied method, and if it's an object of the same class as the wrapped logger,
     * we wrap it before returning.
     * 
     * @deprecated
     */
    public function __call($name, $arguments)
    {
        if (! method_exists($this->wrapped, $name)){
            $stack = (new \Exception)->getTraceAsString();
            $this->warning(sprintf("%s's wrapped logger doesn't know how to %s", static::class, $name), compact('stack'));
            return false;
        }
        $result = $this->wrapped->$name(...$arguments);
        $wrappedCls = get_class($this->wrapped);
        if ($result instanceof $wrappedCls){
            $result = new static($result, $this->context);
        }
        return $result;
    }


}
