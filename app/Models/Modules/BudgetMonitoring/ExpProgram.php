<?php

namespace App\Models\Modules\BudgetMonitoring;
use Illuminate\Database\Eloquent\Model;

class ExpProgram extends Model
{
  protected $table = 'mef_expenditure_program';
  protected $fillable = [
                          "planning_id",
                          "entity_id",
                          "currency",
                          "currency_id",
                          "exchange_rate",
                          "status",
                          "created_by",
                          "modified_by"
                        ];
                        
  public $timestamps = true;
}