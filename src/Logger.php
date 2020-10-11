<?php declare(strict_types=1);

namespace TimDev\StackLogger;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    use StackLoggerTrait;
}
