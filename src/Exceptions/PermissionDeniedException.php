<?php

namespace NaggadimDev\LaravelIspringLearn\Exceptions;


class PermissionDeniedException extends ISpringLearnHTTPException
{
    const DEFAULT_MESSAGE = 'Permission denied';
}