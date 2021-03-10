# PHP Stack Logger

Extend your PSR-3 style logger with context accumulation and callable context elements.

## Inspiration

Inspired by the [similar functionality] in [pinojs]. Implementation details differ, but the core idea remains.

## Approach

This package provides a generic `StackLoggerTrait` that's suitable for use extending PSR-3 implementations that are
patterned `\Psr\Log\AbstractLogger` or `\Psr\Log\LoggerTrait`, where all we need to do is intercept calls to `log()`.
For such loggers, you can simply subclass the logger class and `use StackLoggerTrait`.

Other implementations might require more work. [monolog], for example, predates PSR-3 and has at its core a method named
`addRecord()` (Monolog's `log()`, as well as the level-specific methods all delegate to `addRecord()`). This package 
ships a `MonologStackLoggerTrait` that extends `StackLoggerTrait` to intercept calls to `addRecord()` and `withName()` 
(see comments in the source for more info).

## Justification

After an initial attempt at implementation of a decorator that wraps a logger, it became increasing clear to me that 
extending the logger implementation results in a simpler implementation. For instance, child loggers can have addtional
processors or handlers added, without adding those to the parent. 

## Usage

### Child Loggers

```php
use TimDev\StackLogger\StackLoggerTrait;

// Extend your favorite logger and apply the trait.
class MyLogger extends \Psr\Log\NullLogger
{
    use StackLoggerTrait;
}

$mainLogger = new MyLogger();

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

This can be useful in any situation where want to carry some context through successive calls.

```php
/**
 * Imagine this is a long method that logs a bunch of stuff.
 */
function complexProcessing(User $user, LoggerInterface $logger){
    $logger = $logger->child(['user-id' => $user->id]);
    $logger->info("Begin processing");
    // => [2020-10-17 17:40:53] app.INFO: Begin processing. { "user-id": 123 }
    
    foreach($user->getMemberships() as $membership){
        $l = $logger->child(['membership_id'=>$membership->id]);
        $l->info("Checking membership");
        // => [2020-10-17 17:40:53] app.INFO: Checking membership. { "user-id": 123, 'membership-id' => 1001 }
        if ($membership->isExpired()){
            $l->info('Membership is expired, stopping early.', ['expired-at' => $membership->expiredAt]);
            // => [2020-10-17 17:40:53] app.INFO: Membership is expired, stopping early. { "user-id": 123, "membership-id" => 1001, "expired-at": "2020-06-30T12:00:00Z' }
            continue;
        }
        // ...
        $l->info('Done handling membership');        
        // => [2020-10-17 17:40:53] app.INFO: Done handling membership { "user-id": 123, 'membership-id' => 1001 }
    }
    $logger->info("Finished processing user.");
    // => [2020-10-17 17:40:53] app.INFO: Finished processing user. { "user-id": 123 }
}

}
```

### Callable Context

The other feature provided here is callable context. Any context elements that are `callable` will be invoked at 
logging-time, and the result of the computation will be logged. Callables take a single array argument: 
`function(array $context): mixed`

```php
$startTime = microtime(true);
$logger = (new MyLogger())->child([    
    'elapsed_ms' => fn() => (microtime(true) - $startTime) * 1000000 * 1000,
    'context_count' => fn($ctx) => count($ctx)
]);

// Each message will contain context like: ['elapsed_ms' => 1523, 'context_count' => 2]
```

## To Do

- [ ] Think of a better name than `StackLogger`
- [ ] Maybe provide a Monolog-derived class that composes the trait for convenience
- [ ] Add some tests using other logging implementations (Laminas-Log, Bref, Analog?). 

[similar functionality]: https://getpino.io/#/docs/child-loggers
[pinojs]: https://github.com/pinojs/pino 
[monolog]: https://github.com/Seldaek/monolog
[addRecord]: https://github.com/Seldaek/monolog/blob/a54cd1f1782f62714e4d28651224316bb5540e08/src/Monolog/Logger.php#L278-L336
[withName]: https://github.com/Seldaek/monolog/blob/a54cd1f1782f62714e4d28651224316bb5540e08/src/Monolog/Logger.php#L163-L172
