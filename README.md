# PHP Stack Logger

Wrap your PSR-3 logger with context accumulation and callable context elements.

## Inspiration

Inspired by the [similar functionality] in [pinojs]. Design and implementation
details differ, but the core idea remains: push scoped context on a logger
and have it automatically pop off when the scope ends. This push/pop behavior is
why I think of it as a "stack" logger.

## Approach

The provided [implementation](src/Psr3StackLogger.php) decorates any
implementation of the [PSR3 LoggerInterface], providing implementation of the
additional `withContext` and `addContext` methods defined in this library's
[StackLogger interface](src/StackLogger.php).

Also provided is [`MonologStackLogger`](src/MonologStackLogger.php), which
decorates a `Monolog\Logger` and provides a working [`withName`] implementation.

## Requirements

* PHP >= 8.3
* A PSR-3 compatible logger, such as [Monolog].

## Usage

### Installation

```bash
composer install timdev/stack-logger
```

### Context Stacking

```php
use TimDev\StackLogger\Psr3StackLogger;

// can be anything that implements PSR-3
$yourLogger = $container->get(\Psr\Log\LoggerInterface::class);

// Decorate it 
$mainLogger = new Psr3StackLogger($yourLogger);

// it works like a regular PSR-3 logger.
$mainLogger->info("Hello, World.");
// => [2020-10-17 17:40:53] app.INFO: Hello, World.
$mainLogger->info("Have some Context", ['my' => 'context']);
// => [2020-10-17 17:40:53] app.INFO: Have some Context {"my": "context"}

// but you might want to accumulate some context
$child1 = $mainLogger->withContext(['child' => 'context']);
$child1->info('From a child.', ['call-time' => 'context']);
// => [2020-10-17 17:40:53] app.INFO: From a child. {"child":"context","call-time":"context"}

// but $mainLogger is still around, without the additional context.
$mainLogger->info("Still here, with no accumulated context!");
// => [2020-10-17 17:40:53] app.INFO: Still here, with no accumulated context!
```

This can be useful in any situation where want to carry some context through
successive calls.

```php
/**
 * Imagine this is a long method that logs a bunch of stuff.
 */
function complexProcessing(User $user, \TimDev\StackLogger\StackLogger $logger){
    $logger = $logger->withContext(['user-id' => $user->id]);
    $logger->info('Begin processing');
    // => [2020-10-17 17:40:53] app.INFO: Begin processing. { "user-id": 123 }
    
    foreach($user->getMemberships() as $membership){
        $l = $logger->withContext(['membership_id'=>$membership->id]);
        $l->info('Checking membership');
        // => [2020-10-17 17:40:53] app.INFO: Checking membership. { "user-id": 123, "membership-id" => 1001 }
        if ($membership->isExpired()){
            $l->info('Membership is expired, stopping early.', ['expired-at' => $membership->expiredAt]);
            // => [2020-10-17 17:40:53] app.INFO: Membership is expired, stopping early. { "user-id": 123, "membership-id" => 1001, "expired-at": "2020-06-30T12:00:00Z' }
            continue;
        }
        // ...
        $l->info('Done handling membership');        
        // => [2020-10-17 17:40:53] app.INFO: Done handling membership { "user-id": 123, 'membership-id' => 1001 }
    }
    $logger->info('Finished processing user.');
    // => [2020-10-17 17:40:53] app.INFO: Finished processing user. { "user-id": 123 }
}
```

### Dynamic (Callable) Context

The other feature provided here is callable context. Any context elements that
are `callable` will be invoked at logging-time, and the result of the
computation will be logged. Callables take a single array argument:
`function(array $context): mixed`

```php
$startTime = microtime(true);
$logger = $logger->withContext([    
    'elapsed_ms' => fn() => (microtime(true) - $startTime) * 1000000 * 1000,
    'context_count' => fn($ctx) => count($ctx)
]);
// ... later that day ...
$logger->info('Something happened later.');
// => [2020-10-17 17:40:53] app.INFO: Something happened later. { "elapsed_ms": 1523, "context_count": 2 }
```

**NOTE:** you should carefully consider the performance implications when using
callables in your stacked context. Context is processed *before* invoking the
wrapped logger's methods. The callables will be invoked on every logging method
call, even if the underlying logger is configured to ignore the log-level.

### NullLoggers

All `StackLogger` implementations provide a static `makeNullLogger()` method,
which returns an instance that is configured to discard all log messages. These
"null loggers" can be handy in tests, or as a default logger in classes that
can optionally accept a real logger:

```php
use TimDev\StackLogger\MonologStackLogger;

class SomeService 
{
    public function __construct(?MonologStackLogger $logger = null)
    {
        $this->logger = $logger ?? MonologStackLogger::makeNullLogger();
    }
}
```

## To Do

* [ ] Make MonologStackLogger implement Monolog's ResettableInterface?
* [ ] Consider how this might play with Laravel, the insanely popular PHP
      framework that I don't personally use much. PRs welcome.

[similar functionality]: https://getpino.io/#/docs/child-loggers
[pinojs]: https://github.com/pinojs/pino
[PSR3 LoggerInterface]: https://www.php-fig.org/psr/psr-3/
[`withName`]: https://github.com/Seldaek/monolog/blob/a54cd1f1782f62714e4d28651224316bb5540e08/src/Monolog/Logger.php#L163-L172
[Monolog]: https://github.com/seldaek/monolog
