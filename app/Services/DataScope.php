<?php

namespace App\Services;

use App\User;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Row-level access ("data scope"): which records a role may see, set per role in the role editor.
 *
 *   all     every record (default)
 *   entity  records of the user's unit (users.entity_id)
 *   own     records the user created (created_by)
 *   member  the user's own member record and what belongs to it (users.officer_id)
 *
 * A user with several roles gets the widest scope; Super Admin, console commands and queued jobs see everything.
 * A scope that needs a unit or member the user doesn't have shows nothing rather than everything.
 *
 * Every query of the scoped data goes through one of:
 *   - the HasDataScope trait on the models (Eloquent, automatic),
 *   - DataScope::where($query, $kind, $alias) for query-builder queries (reports),
 *   - DataScope::sql($kind, $alias) for raw SQL (dashboard).
 */
class DataScope
{
    const ALL = 'all';
    const ENTITY = 'entity';
    const OWN = 'own';
    const MEMBER = 'member';

    // Widest first: with several roles the first one found wins
    const LEVELS = [self::ALL, self::ENTITY, self::OWN, self::MEMBER];

    // What kind of data each scoped table holds (decides how a scope filters it)
    const TABLES = [
        'officer' => 'officer',              // the member records themselves
        'income' => 'unit_record',           // has entity_id + created_by
        'expense' => 'unit_record',
        'officer_payable' => 'member_record', // belongs to a member (officer_id)
        'officer_payment' => 'member_record',
        'officer_incentive' => 'member_record',
        'officer_request' => 'member_record',
        'file_manager' => 'file',            // only created_by
    ];

    /** Resolved scopes of this request, by user id */
    protected static array $cache = [];

    /** The signed-in user's scope: ['scope', 'user', 'entity', 'officer'], or null for no restriction */
    public static function current(): ?array
    {
        $user = Auth::user();
        if (!$user) return null;
        if (!array_key_exists($user->id, self::$cache)) {
            self::$cache[$user->id] = self::resolve($user);
        }
        return self::$cache[$user->id];
    }

    /** Resolve again on the next query (after a role's scope or a user's unit / member changed) */
    public static function forget(): void
    {
        self::$cache = [];
    }

    protected static function resolve($user): ?array
    {
        if (Common::isSuperAdmin($user)) return null;
        $scopes = User::find($user->id)?->roles()->pluck('data_scope')->map(fn ($scope) => $scope ?: self::ALL)->all() ?? [];
        $scope = collect(self::LEVELS)->first(fn ($level) => in_array($level, $scopes, true)) ?? self::ALL;
        if ($scope === self::ALL) return null;
        return [
            'scope' => $scope,
            'user' => (int) $user->id,
            'entity' => (int) ($user->entity_id ?? 0),
            'officer' => (int) ($user->officer_id ?? 0),
        ];
    }

    /**
     * The SQL condition limiting $table to the current scope, or null when there is none.
     * $column(name) and $tableName(name) write identifiers (wrapped, prefixed) for the caller's query.
     * Only integers from the users table go into the SQL.
     */
    public static function condition(string $table, callable $column, callable $tableName): ?string
    {
        $scope = self::current();
        $kind = self::TABLES[$table] ?? null;
        if (!$scope || !$kind) return null;

        ['scope' => $level, 'user' => $user, 'entity' => $entity, 'officer' => $officer] = $scope;
        $none = '1 = 0';
        $officersOfUnit = fn () => 'SELECT id FROM '.$tableName('officer').' WHERE entity_id = '.$entity;
        $usersOfUnit = fn () => 'SELECT id FROM '.$tableName('users').' WHERE entity_id = '.$entity;

        switch ($level) {
            case self::ENTITY:
                if (!$entity) return $none;
                return match ($kind) {
                    'officer', 'unit_record' => $column('entity_id').' = '.$entity,
                    'member_record' => $column('officer_id').' IN ('.$officersOfUnit().')',
                    'file' => $column('created_by').' IN ('.$usersOfUnit().')',
                };
            case self::OWN:
                return $column('created_by').' = '.$user;
            case self::MEMBER:
                return match ($kind) {
                    'officer' => $officer ? $column('id').' = '.$officer : $none,
                    'member_record' => $officer ? $column('officer_id').' = '.$officer : $none,
                    'file' => $column('created_by').' = '.$user,
                    'unit_record' => $none, // income / expense are not a member's own data
                };
        }
        return null;
    }

    /** Limit an Eloquent or query-builder query on $table (written as $alias in that query) to the scope */
    public static function where(EloquentBuilder|QueryBuilder $query, string $table, ?string $alias = null): void
    {
        $base = $query instanceof EloquentBuilder ? $query->getQuery() : $query;
        $grammar = $base->getGrammar();
        $alias ??= $table;
        $sql = self::condition($table, fn ($name) => $grammar->wrap($alias.'.'.$name), fn ($name) => $grammar->wrapTable($name));
        if ($sql !== null) $query->whereRaw($sql);
    }

    /** The condition for raw SQL, where $alias is written as-is (already prefixed or a short alias); '1 = 1' when none */
    public static function sql(string $table, string $alias): string
    {
        return self::condition($table, fn ($name) => $alias.'.'.$name, fn ($name) => DB::getTablePrefix().$name) ?? '1 = 1';
    }
}
