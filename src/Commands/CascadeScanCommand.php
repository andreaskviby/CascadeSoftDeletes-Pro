<?php

namespace Stafe\CascadePro\Commands;

use Illuminate\Console\Command;

class CascadeScanCommand extends Command
{
    protected $signature = 'cascade:scan';
    protected $description = 'List models missing cascade mapping';

    public function handle(): int
    {
        $this->warn('Scan command not implemented.');
        return self::SUCCESS;
    }
}
