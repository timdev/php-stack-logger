<?php
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use Psr\Log\Test\TestLogger;
use TimDev\StackLogger\StackLoggerTrait;

class TraitTestLogger extends TestLogger
{
    use StackLoggerTrait;
}

