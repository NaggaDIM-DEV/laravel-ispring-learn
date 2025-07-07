<?php

namespace NaggadimDev\LaravelIspringLearn\Exceptions;


class UnauthorizedException extends ISpringLearnHTTPException
{
    const DEFAULT_MESSAGE = 'Unauthorized';
}