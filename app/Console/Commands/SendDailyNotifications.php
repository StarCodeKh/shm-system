<?php

namespace App\Console\Commands;

use App\Notifications\DailyReminderNotification;
use App\User; // Adjust based on your user model
use Illuminate\Console\Command;

class SendDailyNotifications extends Command
{
    protected $signature = 'notifications:send-daily';

    protected $description = 'Send daily reminder notifications to users';

    public function handle()
    {
        \Log::info('DemoTestCommand ran at: '.now());
        file_put_contents(storage_path('logs/demo.log'), 'Ran: '.now().PHP_EOL, FILE_APPEND);
        $users = User::where('receive_daily_notifications', 1)->get(); // Optional condition
        foreach ($users as $user) {
            $user->notify(new DailyReminderNotification('Here is description'));
        }
        $this->info('Daily notifications sent successfully.');
    }
}
