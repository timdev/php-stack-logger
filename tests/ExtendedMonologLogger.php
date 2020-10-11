<?php
declare(strict_types=1);

namespace TimDev\StackLogger\Test;

use Monolog\Logger;
use TimDev\StackLogger\StackMonologLoggerTrait;

/**
 * Here we extend Monolog\Logger by applying the StackMonologLoggerTrait trait, and add an extra method just for laughs.
 */
class ExtendedMonologLogger extends Logger
{
    use StackMonologLoggerTrait;

    /**
     * You can add your own methods on top.
     * 
     * @return bool
     */
    public function extraMethod(): bool 
    {
        return true;
    }
}
