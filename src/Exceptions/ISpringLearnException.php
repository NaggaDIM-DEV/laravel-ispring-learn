<?php

namespace NaggadimDev\LaravelIspringLearn\Exceptions;

use Exception;
use Throwable;

class ISpringLearnException extends Exception
{
    public function __construct(string $message = 'IspringLearn Exception', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}