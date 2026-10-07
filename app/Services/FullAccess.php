<?php

namespace App\Services;

use ArrayAccess;
use JsonSerializable;

/**
 * Super Admin's permission map: every permission name is granted, including names a controller
 * checks that were never created (e.g. "list_mef_planning"), so Super Admin is never refused.
 *
 * Controllers read the map like an array (isset($map['list_x']) && $map['list_x'] == true), so it
 * answers true for any key. Sent to the app it is the list of real permissions plus "*": true,
 * which the app's can() reads as "everything".
 */
class FullAccess implements ArrayAccess, JsonSerializable
{
    public function __construct(private array $names) {}

    public function offsetExists(mixed $name): bool
    {
        return true;
    }

    public function offsetGet(mixed $name): bool
    {
        return true;
    }

    // Read-only
    public function offsetSet(mixed $name, mixed $value): void {}

    public function offsetUnset(mixed $name): void {}

    public function jsonSerialize(): array
    {
        return ['*' => true] + array_fill_keys($this->names, true);
    }
}
