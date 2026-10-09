<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class InsertDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rows;

    public $tries = 3;

    public $timeout = 120;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function handle()
    {
        DB::table('officer_payable')->insert($this->rows);
    }
}
