# About These Tests

Test methods for the core functionality are defined in the abstract `BaseTest` 
class.

`PsrTestLoggerTest` covers PSR-3 compatibility by testing against a wrapped 
`Psr\Log\TestLogger`, which buffers log messages in an internal array.

`MonologTest` runs the `BaseTest` tests against a wrapped `Monolog\Logger` 
that is configured with a `TestHandler` buffers messages similarly to the PSR3 
`TestLogger`.

`LaminasTest` is similar, but it uses a Laminas logger (configured with a 
`Laminas\Log\Writer\Mock` to buffer messages), which is wrapped in a Laminas
PsrLoggerAdapter, which decorates the core logger and implements PSR3, which is
then wrapped by us to provide our functionality. These tests shouldn't be 
necessary, since it's "just" a PSR-3 logger, but it's nice to validate that our
code works against something other than the `TestLogger` provided by `psr/log`.

The Support/ directory contains test subject classes that extend our 
implementation classes and provide helper methods to inspect the buffered 
messages/records so we can assert stuff about them.

## Improvements?

This stuff is a little tricky to test. If someone came along with a better 
approach I'd be delighted, and offer to buy them a beverage. 

The biggest improvement I'd like to see would be tests that more clearly 
resemble/demonstrate real-world usage.  Ideally without relying on these test-
subject subclasses of the actual classes we want to test.

Unfortunately, I haven't come upw ith anything better.
