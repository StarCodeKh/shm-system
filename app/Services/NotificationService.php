<?php
namespace App\Services;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Contract\Messaging;
use App\User;

class NotificationService
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendNotification(User $user, $title, $body)
    {
        $device_token = "";
        $message = CloudMessage::new()->withToken($device_token)
            ->withNotification(Notification::create($title, $body));

        // if (!$user->device_token) {
        //     return false;
        // }

        // $message = CloudMessage::new()->withToken($user->device_token)
        //     ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            return true;
        } catch (\Throwable $e) {
            logger()->error("Push notification error: " . $e->getMessage());
            return false;
        }
    }
}
