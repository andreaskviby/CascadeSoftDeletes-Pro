<?php

namespace Stafe\CascadePro\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Stafe\CascadePro\CascadeProServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Stafe\\CascadePro\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            CascadeProServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        \Illuminate\Support\Facades\Schema::create('nodes', function ($table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('nodes');
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
