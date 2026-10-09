<?php

namespace App\Services;

use App\Models\Settings\Status;
use DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class PushNotificationService
{
    public static function sendToDevice(string $deviceToken, string $title, string $message, $data = [])
    {
        $firebase = (new Factory)->withServiceAccount(storage_path('firebase/firebase_credentials.json'));
        $messaging = $firebase->createMessaging();
        $message = CloudMessage::new()->withToken($deviceToken)
            ->withNotification([
                'title' => $title,
                'body' => $message,
            ]);
        $messaging->send($message);
    }

    public static function sendToMultipleDevices($title, $message, $type, $data = [])
    {
        // Push Notification to user which has the role to get notify
        $queryUserDevices = DB::table('users as u')
            ->select('u.id', 'ud.device_token')
            ->Join('user_device as ud', 'ud.user_id', '=', 'u.id')
            ->where('u.status', Status::ACTIVE)
            ->where('receive_all_notification', 1)
            ->get();
        $deviceTokens = [];
        foreach ($queryUserDevices as $userDevice) {
            $deviceTokens[] = [
                'user_id' => $userDevice->id,
                'device_token' => $userDevice->device_token,
            ];
        }
        $results = [];
        foreach ($deviceTokens as $token) {
            try {
                $result = PushNotificationService::sendToDevice($token['device_token'], $title, $message, $data);
                DB::table('notifications')->insert([
                    'notifiable_id' => $token['user_id'],
                    'type' => $type,
                    'subject' => $title,
                    'message' => $message,
                    'notifiable_type' => "App\User",
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $results[] = ['token' => $token['device_token'], 'success' => true, 'message_id' => $result];
            } catch (\Exception $e) {
                $results[] = ['token' => $token['device_token'], 'success' => false, 'error' => $e->getMessage()];
            }
        }

        return $results;
    }
}
