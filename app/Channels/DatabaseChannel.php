<?php

namespace App\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Notification;

/**
 * Saves $user->notify(...) rows into tbl_notifications, whose id is auto-increment (not Laravel's uuid)
 * and which also has subject / message columns (the same ones PushNotificationService fills).
 */
class DatabaseChannel extends BaseDatabaseChannel
{
    protected function buildPayload($notifiable, Notification $notification)
    {
        $payload = parent::buildPayload($notifiable, $notification);
        unset($payload['id']);
        $data = $payload['data'];
        $payload['subject'] = $data['title'] ?? class_basename($notification);
        $payload['message'] = $data['message'] ?? null;
        $payload['notifiable_type'] = $notifiable->getMorphClass();
        return $payload;
    }
}
