<?php

namespace App\Models\Mobile;

use Illuminate\Database\Eloquent\Model;

class TaxableSalary extends Model
{
    protected $table = 'taxable_salary';
    protected  $primaryKey = 'id';
    protected $fillable = [
                            'title',
                            'taxable_salary_monthly_from',
                            'taxable_salary_monthly_to',
                            'rate',
                            'currency_symbol',
                            'over_class_tax',
                            'remark',
                            'order_level',
                            'created_by',
                            'modified_by',
                          ];

    public $timestamps = true;

}
