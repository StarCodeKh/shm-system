<?php

namespace App\Http\Middleware;

use App\Services\Common;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

/**
 * Resource writes (store / update / destroy) need the permission their controller declares.
 *
 * Controllers name their permissions in PERMISSION_ACTION_CREATE / _UPDATE / _DELETE
 * (e.g. "create_bank"). Many write methods never checked them, so any signed-in user could
 * change those records. This applies the check to every resource route in one place:
 * - a declared permission that exists is required (403 otherwise);
 * - controllers that declare none, or name a permission that doesn't exist yet, are left as
 *   they were (create the permission in Settings › Roles to protect them).
 *
 * It also drops fields a client must never set on these writes: the record id and timestamps.
 */
class EnforceModulePermission
{
    protected const ACTIONS = [
        'store' => 'CREATE',
        'update' => 'UPDATE',
        'destroy' => 'DELETE',
    ];

    protected const PROTECTED_FIELDS = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $action = $route ? (self::ACTIONS[$route->getActionMethod()] ?? null) : null;
        if (!$action || !is_object($controller = $route->getController())) {
            return $next($request);
        }

        $permission = (fn () => $this->{'PERMISSION_ACTION_'.$action} ?? null)->call($controller);
        if (is_string($permission) && $this->exists($permission) && !Common::can($permission)) {
            return response()->json([
                'success' => false,
                'message' => trans('common.msg_no_permission_access'),
            ], 403);
        }

        if ($action !== 'DELETE') {
            $request->replace($this->withoutProtectedFields($request->all()));
        }

        return $next($request);
    }

    protected function exists(string $permission): bool
    {
        return app(PermissionRegistrar::class)->getPermissions()->contains('name', $permission);
    }

    /** Forms send either { field: value } or a list of single-field objects [{ field: value }, …] */
    protected function withoutProtectedFields(array $input): array
    {
        $strip = fn (array $fields) => array_diff_key($fields, array_flip(self::PROTECTED_FIELDS));

        if (array_is_list($input)) {
            return array_values(array_filter(
                array_map(fn ($item) => is_array($item) ? $strip($item) : $item, $input),
                fn ($item) => $item !== []
            ));
        }

        return $strip($input);
    }
}
