<?php

namespace LaravelQuickLogin;

use Illuminate\Support\ServiceProvider;
use LaravelQuickLogin\Commands\GenerateLoginLinkCommand;

class LaravelQuickLoginServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(OneTimeLoginService::class, fn () => new OneTimeLoginService());
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateLoginLinkCommand::class,
            ]);
        }
    }
}
