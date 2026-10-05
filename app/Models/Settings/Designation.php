<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $table = 'designation';
    protected $fillable = [
                            "name_en",
                            "name_kh",
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = false;
}
