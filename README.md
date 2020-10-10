# PHP Stack Logger

A PSR-3 Logger that can track context.

## Inspiration

Inspired by the [similar functionality] in [pinojs]. Implementation details differ, but the core idea remains.

## Usage (Wrapping)

### Instantiation

This logger decorates any other PSR-3 logger. So, if you're like most people and using [monolog], you simply supply your
monolog instance as the first constructor argument:

```php
// set up your main logger.
$monolog = new Monolog\Logger('myapp');
$monolog->pushHandler(new Monolog\Handler\StreamHandler('php://output', Monolog\Logger::DEBUG));

// Wrap it in StackLogger
$logger = new TimDev\StackLogger\Logger($monolog);
```

### Initial Context

If we want some context included in every message, we can supply that context via the constructor:

```php
$logger = new TimDev\StackLogger\Logger($loggerToWrap, ['some' => 'context']);
$logger->info('Reticulated 7 splines'); 
// =>  [2020-10-09 12:40:48] myapp.INFO: Reticulated 7 splines {"some": "context"}
$logger->debug('Ate a bagel', ['foo' => 'bar']); 
// => [2020-10-09 12:40:48] myapp.DEBUG: Ate a bagel {"some": "context", "foo": "bar"} 
```

### Child Loggers

Like in Pino, you can layer more context on top using child loggers. 

```php
$logger = new Logger($loggerToWrap, ['some' => 'context']);
$logger->info('Hello');
// => [2020-10-09 12:40:48] myapp.INFO: Hello {"some": "context"}

$child = $logger->child(['more' => 'cowbell']);
$child->debug('Not fearing the reaper');
// => [2020-10-09 12:40:48] myapp.DEBUG: Not fearing the reaper {"some": "context", "more": "cowbell"}

# earlier context can be overwritten
$grandchild = $child->child(["more" => "COWBELL!!!!"]);
$grandchild->debug('Even More');
// => [2020-10-09 12:40:48] myapp.DEBUG: Not fearing the reaper {"some": "context", "more": "COWBELL!!!!"}
```

### Callabes in Context

Context can also contain closures with a signature `function(array $context): mixed`, where `$context` is the full 
context.

```php
$startTime = null; 
$elapsed = function($context){ return time() - $context['startTime']; };
$child = $logger->child(['elapsed'=>$elapsed, 'startTime'=>$startTime);
$startTime = time();
doExpensiveThing();  // takes 60 seconds to run.
$child->info('Finished doing expensive thing');
// => [2020-10-09 12:40:48] myapp.DEBUG: Not fearing the reaper {"startTime": 1602247188, "elapsed": 60}
```

## Usage (Traits)

### Motivation

These traits are experimental.

One drawback to using the wrapping method is that your application may actually depend on some logger has an interface
larger than that defined by PSR-3. For instance, monolog exposes public [addRecord] and [withName] methods, and you
may have existing application code that calls them. 

To alleviate this, `TimDev\StackLogger\Logger` implements `__call` to proxy calls to non-PSR-3 methods to the wrapped
logger. If the wrapped logger doesn't implement the method either, a WARNING is logged.

The magic `__call()` approach works well enough, even though __call is somewhat smelly, and may make your IDE complain.

The two traits included in this package provide an alternative approach, which may be better in the long-run.:

* **StackLoggerTrait** - can generically extend any PSR-3 logger. It overrides the `log()` method on the base class, and
                         exposes the `child()` method that enables the context-stacking functionality.
                         
* **StackMonologLoggerTrait** - extends `StackLoggerTrait` to override `Monolog\Logger::addRecord()` with an 
                                implementation almost identical to `StackLoggerTrait::log()`.
                         
To use these traits, you create your own logger class that extends the main logger class, and applies one of the traits.

**With Monolog**

```php 
class Logger extends \Monolog\Logger
{
    use TimDev\StackLogger\StackMonologLoggerTrait;
}

// It's 'just' monolog!
$logger = new Logger('myapp');
$logger->pushHandler(new Monolog\Handler\StreamHandler('php://output', Monolog\Logger::DEBUG));

$logger->info('Hello, world');

// But with a few new tricks:
$child = $logger->child(['some' => 'context']);
$child->addRecord
```



[similar functionality]: https://getpino.io/#/docs/child-loggers
[pinojs]: https://github.com/pinojs/pino 
[monolog]: https://github.com/Seldaek/monolog
[addRecord]: https://github.com/Seldaek/monolog/blob/a54cd1f1782f62714e4d28651224316bb5540e08/src/Monolog/Logger.php#L278-L336
[withName]: https://github.com/Seldaek/monolog/blob/a54cd1f1782f62714e4d28651224316bb5540e08/src/Monolog/Logger.php#L163-L172
