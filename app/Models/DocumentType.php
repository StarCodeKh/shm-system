<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'document_type';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            'document_type',
                            'order_level',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = true;
}
