<?php

// app/Console/Commands/GenerateMonthlyPayables.php

namespace App\Console\Commands;

use App\Models\OfficerPayable;
use App\Models\OfficerPaymentRecurring;
use App\Models\Settings\Account;
use App\Models\Settings\Status;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateMonthlyPayables extends Command
{
    protected $signature = 'payables:generate {--month= : The month to generate payables for (YYYY-MM)}';

    protected $description = 'Generates monthly payable records for members based on recurring schedules.';

    public function handle()
    {
        $expDueDates = [];
        $targetMonth = $this->option('month')
            ? Carbon::parse($this->option('month'))->startOfMonth()
            : Carbon::now()->startOfMonth();
        $this->info('Generating payables for: '.$targetMonth->format('Y-m'));
        $count = 0;
        // Chunk through active recurring payment schedules
        OfficerPaymentRecurring::where('status', Status::ACTIVE)->where('is_default', 1)
            ->chunkById(500, function ($schedules) use ($targetMonth, &$count) {
                foreach ($schedules as $schedule) {
                    // Check if the schedule is active for the target month
                    if (! $schedule->isActiveForMonth($targetMonth)) {
                        continue;
                    }
                    // Determine the expected due date for this schedule in the target month
                    $expectedDueDate = $schedule->getNextDueDate($targetMonth, $schedule->officer_id);
                    // If the expected due date falls within the target month and a payable isn't already pending/paid
                    if ($expectedDueDate->month === $targetMonth->month && $expectedDueDate->year === $targetMonth->year) {
                        $existingPayable = OfficerPayable::where('officer_id', $schedule->officer_id)
                            ->whereYear('due_date', $targetMonth->year)
                            ->whereMonth('due_date', $targetMonth->month)
                            ->exists();

                        if (! $existingPayable) {
                            DB::transaction(function () use ($schedule, $expectedDueDate, &$count) {
                                OfficerPayable::create([
                                    'officer_id' => $schedule->officer_id,
                                    'account_id' => Account::contributionId($schedule->payment_term),
                                    'payment_method_id' => 0,
                                    'amount' => $schedule->recurring_amount,
                                    'currency_id' => $schedule->currency_id,
                                    'payment_term' => $schedule->payment_term,
                                    'due_date' => $expectedDueDate,
                                    'remark' => ucfirst($schedule->payment_term).' subscription for '.$expectedDueDate->format('F Y'),
                                    'status' => Status::UNPAID, // Unpaid Status
                                ]);
                                $count++;
                            });
                        }
                    }
                }
            });

        $this->info('Generated '.$count.' payable records.');

        return Command::SUCCESS;
    }
}
