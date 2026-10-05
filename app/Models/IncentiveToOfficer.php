<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class IncentiveToOfficer extends Model
{
    protected $table = 'incentive_to_officer';
    protected  $primaryKey = 'id';
    protected $fillable = [
        "incentive_id",
        "officer_id",
        "entity",
        "entity_id",
        "position",
        "duty",
        "bank",
        "bank_account_no",
        "bank_code",
        "identity_card",
        "index",
        "achievement",
        "reference_id",
        "incentive",
        "tax",
        "return_amount",
        "incentive_after_tax",
        "reminder_amount",
        "remark",
        "issue_from_date",
        "issue_to_date",
        "is_notify",
        "created_by",
        "bank_id",
    ];
    public $timestamps = true;
}