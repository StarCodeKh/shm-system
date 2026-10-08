<?php

namespace App\Providers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Models\Settings\Status;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // notify() rows fit tbl_notifications (auto-increment id, subject / message)
        $this->app->bind(\Illuminate\Notifications\Channels\DatabaseChannel::class, \App\Channels\DatabaseChannel::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // oauth_clients uses auto-increment integer ids
        \Laravel\Passport\Passport::$clientUuids = false;

        $this->registerQueryMacros();
    }

    /**
     * Shared query helpers, usable on DB::table() and Eloquent queries.
     */
    private function registerQueryMacros()
    {
        // Rows that are not soft-deleted (status 4). Bracketed, so it never cancels the other filters:
        //   ... AND (status NOT IN (4) OR status IS NULL)
        Builder::macro('whereNotDeleted', function ($column = 'status') {
            return $this->where(function ($query) use ($column) {
                $query->whereNotIn($column, [Status::DELETED])->orWhereNull($column);
            });
        });

        // Option lists: the admin-defined order_level, then id. Tables without order_level sort by id.
        Builder::macro('orderByLevel', function ($direction = 'asc') {
            static $columns = [];
            $from = is_string($this->from) ? preg_split('/\s+as\s+/i', $this->from) : [null];
            $table = $from[0];
            $alias = $from[1] ?? $table;
            if ($table && !isset($columns[$table])) {
                $columns[$table] = Schema::getColumnListing($table);
            }
            $known = $columns[$table] ?? [];
            if (in_array('order_level', $known)) {
                $this->orderBy($alias . '.order_level', $direction);
            } elseif ($this->joins) {
                $this->orderBy('order_level', $direction); // may come from a joined table
            }
            if (in_array('id', $known)) {
                $this->orderBy($alias . '.id');
            }
            return $this;
        });

        // Sort a list by the requested column, newest first for rows with the same value.
        // A sort column the table doesn't have is ignored instead of failing the query.
        Builder::macro('orderByFilter', function ($filter) {
            static $columns = [];
            $from = is_string($this->from) ? preg_split('/\s+as\s+/i', $this->from) : [null];
            $table = $from[0];
            $alias = $from[1] ?? $table;
            if ($table && !isset($columns[$table])) {
                $columns[$table] = Schema::getColumnListing($table);
            }
            $known = $columns[$table] ?? [];

            $sort = $filter['sort'] ?? null;
            $order = strtolower($filter['order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
            $plain = is_string($sort) && strpos($sort, '.') === false;
            if ($sort && (!$plain || $this->joins || in_array($sort, $known))) {
                $this->orderBy($sort, $order);
            }
            if (in_array('id', $known) && $sort !== 'id' && $sort !== $alias . '.id') {
                $this->orderBy($alias . '.id', 'desc');
            }
            return $this;
        });
    }
}
