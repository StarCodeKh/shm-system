<?php

namespace App\Models\Concerns;

use App\Services\DataScope;
use Illuminate\Database\Eloquent\Builder;

/**
 * Every Eloquent query on the model only returns the records the signed-in user's role may see
 * (App\Services\DataScope). Lists, details, edits, deletes and reports all follow it.
 * Model::withoutGlobalScope('data_scope') reads past it (system work only).
 */
trait HasDataScope
{
    protected static function bootHasDataScope(): void
    {
        static::addGlobalScope('data_scope', function (Builder $query) {
            DataScope::where($query, $query->getModel()->getTable());
        });
    }
}
