<?php

namespace Ashiful\Exco;


use Ashiful\Exco\Commands\ClearLogCommand;
use Ashiful\Exco\Commands\CreateBladeCommand;
use Ashiful\Exco\Commands\CreateRepositoryCommand;
use Ashiful\Exco\Commands\CreateServiceCommand;
use Ashiful\Exco\Commands\CreateTraitCommand;
use Illuminate\Support\ServiceProvider;

class ExcoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateRepositoryCommand::class,
                CreateTraitCommand::class,
                CreateServiceCommand::class,
                CreateBladeCommand::class,
                ClearLogCommand::class,
            ]);
        }
    }

    public function register()
    {

    }
}
