<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DailyReminderNotification extends Notification
{
    use Queueable;
    public $incrementing = true;  
    protected $keyType = 'int';
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $message;
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','broadcast'];  // or ['database', 'mail', 'broadcast']
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
        // return (new MailMessage)
        //             ->subject('Daily Reminder')
        //             ->greeting('Hello ' . $notifiable->name)
        //             ->line('This is your scheduled daily reminder.')
        //             ->action('View Dashboard', url('/dashboard'))
        //             ->line('Thank you for using our application!');
    }

    // Optional: Save to database
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Daily Reminder',
            'message' => 'Don’t forget to check your tasks today!',
            'action_url' => url('/dashboard'),
            'image' => null,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Daily Reminder',
            'message' => $this->message,
            'action_url' => '/dashboard',
        ];
    }
}
