<?php

namespace Stafe\CascadePro\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stafe\CascadePro\CascadeSoftDeletes;
use Stafe\CascadePro\Tests\Fixtures\Node;

class Tag extends Model
{
    use CascadeSoftDeletes, SoftDeletes;

    protected $fillable = ['name'];

    protected array $cascadeDeletes = [];

    public function nodes()
    {
        return $this->belongsToMany(Node::class)->withPivot('deleted_at');
    }
}
