<?php

namespace Stafe\CascadePro\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stafe\CascadePro\CascadeSoftDeletes;

class Node extends Model
{
    use CascadeSoftDeletes, SoftDeletes;

    protected $fillable = ['name', 'parent_id'];

    protected array $cascadeDeletes = ['children'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
