<?php

namespace App\Console\Commands;

use App\Notifications\RecurringPaymentNotification;
use App\User;
use Illuminate\Console\Command;

class SendRecurringPaymentNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:recurring-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('DemoTestCommand ran at: '.now());
        file_put_contents(storage_path('logs/demo.log'), 'Ran: '.now().PHP_EOL, FILE_APPEND);
        $users = User::where('receive_daily_notifications', 1)->get(); // Optional condition
        foreach ($users as $user) {
            $user->notify(new RecurringPaymentNotification);
        }
        $this->info('Payment Recurring notifications sent successfully.');
    }
}
