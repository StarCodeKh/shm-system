<?php

namespace App\Jobs;

use App\Models\OfficerPayable;
use App\Models\OfficerPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Settings\Status;

// Assuming you have a PaymentGateway service/facade
// use App\Services\PaymentGateway;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int[]
     */
    public $backoff = [5, 10, 30];

    /**
     * Create a new job instance.
     */
    public function __construct(OfficerPayable $payable)
    {
        $this->payable = $payable;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Ensure the payable is still pending and not being processed by another job
        DB::transaction(function () {
            $payable = OfficerPayable::where('id', $this->payable->id)
                                ->where('status', Status::UNPAID) // Check Unpaid Status
                                ->lockForUpdate()
                                ->first();

            if (!$payable) {
                Log::info('Payable ' . $this->payable->id . ' already processed or not pending.');
                return; // Already processed or status changed
            }

            $payable->update(['status' => Status::UNPAID]); // Status = 15 not yet paid
        });

        try {
            // Simulate payment gateway interaction
            // In a real application, this would call your PaymentGateway service
            $transactionId = 'txn_' . Str::random(20);
            $paymentSuccessful = (bool) random_int(0, 1); // Simulate success/failure

            if ($paymentSuccessful) {
                DB::transaction(function () use ($payable, $transactionId) {
                    $payable->update([
                        'status' => 'paid', // Status = 19 means paid successfully
                    ]);

                    OfficerPayment::create([
                        'account_id' => $payable->account_id,
                        'officer_id' => $payable->officer_id,
                        'ref_payable_id' => $payable->id,
                        'receipt_no' => $transactionId,
                        'amount' => $payable->amount,
                        'payment_date' => $payable->payment_date,
                        'issue_date' => $payable->due_date,
                        'currency_id' => $payable->currency_id,
                        'exchange_rate' => $payable->exchange_rate,
                        'payment_method_id' => $payable->payment_method_id,
                        'note' => 'Payment successful for ' . $payable->description,
                        'attach' => $payable->attach,
                        'created_by' => $payable->created_by,
                        'modified_by' => $payable->modified_by,
                        'status' => 'paid', // Status = 19 means paid successfully
                    ]);

                    // Increment executed_num on the associated OfficerPaymentRecurring record
                    if ($payable->officerPaymentRecurring) {
                        $payable->officerPaymentRecurring->increment('executed_num');
                    }
                });

                Log::info('Payment for payable ' . $payable->id . ' successful. Transaction ID: ' . $transactionId);
            } else {
                // Payment failed at gateway
                DB::transaction(function () use ($payable, $transactionId) {
                    $payable->update([
                        'status' => 24, // Status = 24 means error status
                    ]);

                    OfficerPayment::create([
                        'account_id' => $payable->account_id,
                        'officer_id' => $payable->officer_id,
                        'ref_payable_id' => $payable->id,
                        'receipt_no' => $transactionId,
                        'amount' => $payable->amount,
                        'payment_date' => $payable->payment_date,
                        'issue_date' => $payable->due_date,
                        'currency_id' => $payable->currency_id,
                        'exchange_rate' => $payable->exchange_rate,
                        'payment_method_id' => $payable->payment_method_id,
                        'attach' => $payable->attach,
                        'created_by' => $payable->created_by,
                        'modified_by' => $payable->modified_by,
                        'note' => 'Payment failed for ' . $payable->description,
                        'status' => 24, // Status = 24 means error status
                    ]);
                });

                Log::warning('Payment for payable ' . $payable->id . ' failed. Transaction ID: ' . $transactionId);
                // Optionally, re-throw to trigger job retry based on $tries
                // throw new Exception('Payment gateway declined transaction.');
            }
        } catch (\Throwable $e) {
            // Catch any exceptions during payment processing
            DB::transaction(function () use ($payable, $e) {
                $payable->update(['status' => 'failed']);
                OfficerPayment::create([
                    'account_id' => $payable->account_id,
                    'officer_id' => $payable->officer_id,
                    'ref_payable_id' => $payable->id,
                    'receipt_no' => $transactionId,
                    'amount' => $payable->amount,
                    'payment_date' => $payable->payment_date,
                    'issue_date' => $payable->due_date,
                    'currency_id' => $payable->currency_id,
                    'exchange_rate' => $payable->exchange_rate,
                    'payment_method_id' => $payable->payment_method_id,
                    'attach' => $payable->attach,
                    'created_by' => $payable->created_by,
                    'modified_by' => $payable->modified_by,
                    'status' => 24, // Status = 24 means error status
                    'note' => 'Payment processing error: ' . $e->getMessage(),
                ]);
            });
            Log::error('Error processing payment for payable ' . $payable->id . ': ' . $e->getMessage());
            $this->fail($e); // Mark job as failed and move to failed_jobs table
        }
    }
}

?>
