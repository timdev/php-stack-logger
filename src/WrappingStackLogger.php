<?php

declare(strict_types=1);

namespace TimDev\StackLogger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerInterface as PsrInterface;

/**
 * This interface is for StackLogger implementations that wrap some variety of
 * PSR-3 logger. It only really exists so that getNullLogger() can safely do
 * `new static(...)`.
 */
interface WrappingStackLogger extends LoggerInterface
{
    public function __construct(PsrInterface $logger);
}
