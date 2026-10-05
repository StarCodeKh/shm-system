<?php 
namespace App\Services;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\FcmChannel;
class PushNotification extends Notification
{
    protected $title;
    protected $body;

    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    public function via($notifiable)
    {
        // return ['fcm'];
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return (new FcmMessage(notification: new \NotificationChannels\Fcm\Resources\Notification(
                title: $this->title,
                body: $this->body,
            )))
            ->data(['key' => 'value']); // optional
    }
}
