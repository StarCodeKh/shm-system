<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Schema;

/**
 * The columns of a model's list screen (data_fields of its index endpoint), in order: the model's LIST_FIELDS
 * that its table has. Links such as entity_id, officer_id and status are shown by name (DTableList customField).
 * Models without LIST_FIELDS keep dataFields() (their required columns).
 */
trait HasListFields
{
    public static function listFields(): array
    {
        return array_values(array_intersect(static::LIST_FIELDS, Schema::getColumnListing((new static)->getTable())));
    }
}
