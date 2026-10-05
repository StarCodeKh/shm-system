<?php

namespace App\Models\Modules\ProgramManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules\ProgramManagement\Program;
use DB;

class CostingToEntity extends Model
{
  protected $table = 'mef_costing_to_entity';
  protected $primaryKey = 'id';
  protected $fillable = [
                          'id',
                          'planning_id',
                          'entity_id',
                          'sub_program_id',
                          'cluster_activity_id',
                          'activity_id',
                          'task_id',
                          'code',
                          "name_en",
                          "name_kh",
                          'remark',
                          'order_level'
                        ];
  public $timestamps = true;


}
