<?php

namespace App\Models\Modules\BudgetArrangement;

use Illuminate\Database\Eloquent\Model;

class BSPClusterActToEntity extends Model
{
    protected $table = 'mef_cluster_activity_to_entity';
    protected $fillable = [
                            'planning_id',
                            'sub_program_id',
                            'cluster_activity_id',
                            'entity_member_id',
                            'entity_id',
                            'created_by',
                            'modified_by'
                        ];
    public $timestamps = true;
}
