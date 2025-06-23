<?php

namespace Stafe\CascadePro;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stafe\CascadePro\Commands\CascadeFlushCommand;
use Stafe\CascadePro\Commands\CascadeScanCommand;

class CascadeProServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('cascadepro')
            ->hasConfigFile('cascadepro')
            ->hasCommand(CascadeScanCommand::class)
            ->hasCommand(CascadeFlushCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->publishes([
            __DIR__.'/../config/cascadepro.php' => config_path('cascadepro.php'),
        ], 'cascadepro-config');
    }
}
