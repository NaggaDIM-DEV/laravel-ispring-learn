<?php

namespace NaggadimDev\LaravelIspringLearn\Exceptions;


class BadRequestException extends ISpringLearnHTTPException
{
    const DEFAULT_MESSAGE = 'Bad Request';
}