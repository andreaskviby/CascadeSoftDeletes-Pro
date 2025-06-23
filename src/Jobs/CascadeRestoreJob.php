<?php

namespace Stafe\CascadePro\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CascadeRestoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $ids, public string $model)
    {
    }

    public function handle(): void
    {
        ($this->model)::withTrashed()->whereKey($this->ids)->get()->each->restore();
    }
}
