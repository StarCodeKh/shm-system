<?php

    namespace App\Models\Modules\BudgetArrangement;

    use Illuminate\Database\Eloquent\Model;

    class BSPActToEntity extends Model
    {
        protected $table = 'mef_activity_to_entity';
        protected $fillable = [
                                'activity_id',
                                'cluster_activity_id',
                                'planning_id',
                                'entity_id',
                                'entity_member_id',
                                'created_by',
                                'modified_by'
                            ];
        public $timestamps = true;
    }