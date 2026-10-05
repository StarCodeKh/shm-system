<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sector';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            'name',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = true;
}
