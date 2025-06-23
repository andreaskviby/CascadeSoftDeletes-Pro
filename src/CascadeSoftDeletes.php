<?php

namespace Stafe\CascadePro;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes as LaravelSoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Stafe\CascadePro\Events\DeletedCascade;
use Stafe\CascadePro\Events\DeletingCascade;
use Stafe\CascadePro\Events\RestoredCascade;
use Stafe\CascadePro\Events\RestoringCascade;
use Stafe\CascadePro\Jobs\CascadeRestoreJob;
use Stafe\CascadePro\Jobs\CascadeSoftDeleteJob;

trait CascadeSoftDeletes
{
    use LaravelSoftDeletes;

    public static function bootCascadeSoftDeletes(): void
    {
        static::deleted(function (Model $model) {
            if (! $model->isForceDeleting()) {
                $model->runCascadeDelete();
            }
        });

        static::restoring(function (Model $model) {
            $model->runCascadeRestore();
        });
    }

    protected function runCascadeDelete(): void
    {
        foreach ($this->getCascadeRelations() as $relation) {
            $models = $relation->get();

            event(new DeletingCascade($this, $models));

            $this->processPivotDeletion($relation);

            $this->handleModels($models, CascadeSoftDeleteJob::class, 'delete');

            event(new DeletedCascade($this, $models));
        }
    }

    protected function runCascadeRestore(): void
    {
        foreach ($this->getCascadeRelations() as $relation) {
            $models = $relation->withTrashed()->get();

            event(new RestoringCascade($this, $models));

            $this->processPivotDeletion($relation, restoring: true);

            $this->handleModels($models, CascadeRestoreJob::class, 'restore');

            event(new RestoredCascade($this, $models));
        }
    }

    protected function handleModels(Collection $models, string $jobClass, string $method): void
    {
        $chunk = config('cascadepro.chunk_size', 1000);
        $connection = config('cascadepro.queue_connection', config('queue.default'));

        if ($models->count() > $chunk) {
            $models->chunk($chunk)->each(function ($chunked) use ($jobClass, $connection) {
                dispatch(new $jobClass($chunked->modelKeys(), $chunked->first()::class))
                    ->onConnection($connection);
            });
        } else {
            $models->each(fn ($model) => $model->$method());
        }
    }

    protected function getCascadeRelations(): array
    {
        $relations = [];

        if (method_exists($this, 'cascadeThrough')) {
            foreach (Arr::wrap($this->cascadeThrough()) as $relation) {
                if ($relation instanceof Relation) {
                    $relations[] = $relation;
                }
            }
        }

        if (property_exists($this, 'cascadeDeletes')) {
            foreach ($this->cascadeDeletes as $name) {
                if ($rel = $this->resolveRelation($name)) {
                    $relations[] = $rel;
                }
            }
        }

        return $relations;
    }

    protected function resolveRelation(string $name): ?Relation
    {
        $parts = explode('.', $name);
        $relation = $this;

        foreach ($parts as $part) {
            if (! method_exists($relation, $part)) {
                return null;
            }

            $relation = $relation->$part();
        }

        return $relation instanceof Relation ? $relation : null;
    }

    protected function processPivotDeletion(Relation $relation, bool $restoring = false): void
    {
        if (! method_exists($relation, 'getTable')) {
            return;
        }

        $table = $relation->getTable();
        if (! in_array($table, config('cascadepro.pivot_tables', []))) {
            return;
        }

        $query = $relation->newPivotQuery();
        $hasDeletedAt = Schema::hasColumn($table, 'deleted_at');

        if ($restoring) {
            if ($hasDeletedAt) {
                $query->whereNotNull('deleted_at')->update(['deleted_at' => null]);
            }

            return;
        }

        if ($hasDeletedAt) {
            $query->whereNull('deleted_at')->update(['deleted_at' => now()]);
        } else {
            $query->delete();
        }
    }
}
