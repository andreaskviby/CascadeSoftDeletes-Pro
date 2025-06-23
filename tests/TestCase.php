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

        config()->set('cascadepro.pivot_tables', ['node_tag']);

        \Illuminate\Support\Facades\Schema::create('nodes', function ($table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('nodes');
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\Schema::create('tags', function ($table) {
            $table->id();
            $table->string('name');
            $table->softDeletes();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\Schema::create('node_tag', function ($table) {
            $table->foreignId('node_id')->constrained('nodes');
            $table->foreignId('tag_id')->constrained('tags');
            $table->timestamp('deleted_at')->nullable();
        });
    }
}
