<?php

namespace NaggadimDev\LaravelIspringLearn\Facades;

use Illuminate\Support\Facades\Facade;
use NaggadimDev\LaravelIspringLearn\Contracts\ISpringLearnServiceContract;

/**
 * @mixin ISpringLearnServiceContract
 */
class ISpringLearn extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ISpringLearnServiceContract::class;
    }
}
