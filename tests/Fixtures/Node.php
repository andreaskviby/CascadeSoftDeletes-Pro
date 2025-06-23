<?php

namespace Stafe\CascadePro\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stafe\CascadePro\CascadeSoftDeletes;
use Stafe\CascadePro\Tests\Fixtures\Tag;

class Node extends Model
{
    use CascadeSoftDeletes, SoftDeletes;

    protected $fillable = ['name', 'parent_id'];

    protected array $cascadeDeletes = ['children', 'tags'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withPivot('deleted_at');
    }
}
