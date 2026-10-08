<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Configuration extends Model
{
    protected $table = 'config';
    protected $primaryKey = 'id';
    protected $fillable = [
        'group',
        'name',
        'keyword',
        'value',
        ];
    public $timestamps = false;

    // Any change to a setting clears the cached copy read by setting()
    protected static function booted()
    {
        static::saved(fn () => \App\Services\Settings::forget());
        static::deleted(fn () => \App\Services\Settings::forget());
    }

    public static function getConfigs($filter){
        $data = array();
        $whereClause = Configuration::orderBy("key","asc");
        $results = collect($whereClause->get());
        foreach($results as $row){
            $data[$row->key] = $row->value;
        }
        return $data;
    }
}
