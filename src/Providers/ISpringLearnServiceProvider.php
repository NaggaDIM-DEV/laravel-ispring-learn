<?php

namespace NaggadimDev\LaravelIspringLearn\Providers;

use Illuminate\Support\ServiceProvider;
use NaggadimDev\LaravelIspringLearn\Concretes\ISpringLearnService;
use NaggadimDev\LaravelIspringLearn\Contracts\ISpringLearnServiceContract;

class ISpringLearnServiceProvider extends ServiceProvider
{
    public $bindings = [
        ISpringLearnServiceContract::class => ISpringLearnService::class,
    ];

    public function register(): void
    {

    }

    public function boot(): void
    {
        if($this->app->runningInConsole()){
            $this->publishes([
                __DIR__.'/../../config/ispring-learn.php' => config_path('ispring-learn.php'),
            ]);
        }
    }
}
