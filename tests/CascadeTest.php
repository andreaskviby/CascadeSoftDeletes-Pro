<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Stafe\CascadePro\Events\DeletedCascade;
use Stafe\CascadePro\Events\DeletingCascade;
use Stafe\CascadePro\Events\RestoredCascade;
use Stafe\CascadePro\Events\RestoringCascade;
use Stafe\CascadePro\Jobs\CascadeRestoreJob;
use Stafe\CascadePro\Jobs\CascadeSoftDeleteJob;
use Stafe\CascadePro\Tests\Fixtures\Node;

it('cascades deletes and restores', function () {
    $parent = Node::create(['name' => 'p']);
    $child = Node::create(['name' => 'c', 'parent_id' => $parent->id]);

    $parent->delete();

    expect(Node::withTrashed()->find($child->id)->trashed())->toBeTrue();

    Node::withTrashed()->find($parent->id)->restore();

    expect(Node::find($child->id))->not->toBeNull();
});

it('fires events', function () {
    Event::fake();
    $parent = Node::create(['name' => 'p']);
    Node::create(['name' => 'c', 'parent_id' => $parent->id]);

    $parent->delete();
    Event::assertDispatched(DeletingCascade::class);
    Event::assertDispatched(DeletedCascade::class);

    Node::withTrashed()->find($parent->id)->restore();
    Event::assertDispatched(RestoringCascade::class);
    Event::assertDispatched(RestoredCascade::class);
});

it('chunks large sets', function () {
    config()->set('cascadepro.chunk_size', 1);
    Bus::fake();

    $parent = Node::create(['name' => 'p']);
    Node::create(['name' => 'a', 'parent_id' => $parent->id]);
    Node::create(['name' => 'b', 'parent_id' => $parent->id]);

    $parent->delete();
    Bus::assertDispatched(CascadeSoftDeleteJob::class, 2);
});
