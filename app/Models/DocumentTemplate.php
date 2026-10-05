<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $table = 'document_type';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            'document_type_id',
                            'document_name',
                            'description',
                            'status',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = true;
}
