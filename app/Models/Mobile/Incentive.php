<?php

namespace App\Models\Mobile;

use Illuminate\Database\Eloquent\Model;
use DB;
class Incentive extends Model
{
    protected $table = 'incentive';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            'title',
                            'sent_to',
                            'khmer_date',
                            'khmer_day',
                            'prepared_by',
                            'seen_by',
                            'approved_by',
                            'reference',
                            'letter_type',
                            'issue_date',
                            'issue_from_date',
                            'issue_to_date',
                            'effective_date',
                            'attach_fille',
                            'is_approve',
                            'created_by',
                            'modified_by'
                          ];
    public $timestamps = true;

    public static function getIncentiveByOfficers($filter){
      $userId = $filter["userId"];
      $query = DB::table("incentive_to_officer as i2o")
               ->Select("i.title", "i2o.incentive_after_tax", "i2o.issue_from_date", "i2o.remark")
               ->Join("incentive_officer as io", "io.id", "=", "i2o.officer_id")
               ->Join("incentive as i", "i.id", "=", "i2o.incentive_id")
               ->Where("io.user_id", $userId)
               ->get();
      return $query;
    }

    public static function getSummary($userId){
      $query = DB::table("incentive_to_officer as i2o")
               ->Join("incentive_officer as io", "io.id", "=", "i2o.officer_id")
               ->Where("io.user_id", $userId)
               ->sum("i2o.incentive");
      return $query;
    }
}