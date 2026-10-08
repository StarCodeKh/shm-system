<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPaymentJob;
use App\Models\OfficerPayable;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\Settings\Status;

class DispatchPendingPayments extends Command
{
    protected $signature = 'payments:dispatch {--month= : The month to dispatch payments for (YYYY-MM)}';
    protected $description = 'Dispatches pending payment jobs for payables.';

    public function handle()
    {
        $targetMonth = $this->option('month')
            ? Carbon::parse($this->option('month'))->startOfMonth()
            : Carbon::now()->startOfMonth();
        $this->info('Dispatching pending payments for: ' . $targetMonth->format('Y-m'));

        $count = 0;
        OfficerPayable::where('status', Status::UNPAID) // Status pending = 15
            ->chunkById(500, function ($payables) use (&$count) {
                foreach ($payables as $payable) {
                    ProcessPaymentJob::dispatch($payable)->onQueue('payments');
                    $count++;
                }
            });

        $this->info('Dispatched ' . $count . ' payment jobs.');
        return Command::SUCCESS;
    }
}
?>
