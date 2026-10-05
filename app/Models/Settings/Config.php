<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'config';
    protected $primaryKey = 'id';
    protected $fillable = [
                            'entity_id',
                            'code',
                            'key',
                            'value',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = false;
}