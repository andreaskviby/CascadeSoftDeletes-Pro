<?php

namespace Stafe\CascadePro\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;

class CascadeFlushCommand extends Command
{
    protected $signature = 'cascade:flush {model}';
    protected $description = 'Hard delete already-soft-deleted trees for a model';

    public function handle(): int
    {
        $class = $this->argument('model');
        if (!is_subclass_of($class, Model::class)) {
            $this->error('Invalid model given.');
            return self::FAILURE;
        }

        $class::onlyTrashed()->get()->each->forceDelete();
        $this->info('Flushed trashed records.');
        return self::SUCCESS;
    }
}
