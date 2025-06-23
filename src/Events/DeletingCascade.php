<?php

namespace Stafe\CascadePro\Events;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DeletingCascade
{
    public function __construct(public Model $root, public Collection $batch)
    {
    }
}
