<?php

namespace App\Services;

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseNotificationService
{
    public function sendToDevice($deviceToken, $title, $body, $data = [])
    {
        $notification = Notification::fromArray([
            'title' => $title,
            'body' => $body,
        ]);
        
        $message = CloudMessage::new()->withToken($deviceToken)
            ->withNotification($notification)
            ->withData($data);
        
        return Firebase::messaging()->send($message);
    }
    
    public function sendToUser($user, $title, $body, $data = [])
    {
        $results = [];
        foreach ($user->deviceTokens as $deviceToken) {
            try {
                $result = $this->sendToDevice(
                    $deviceToken->device_token, 
                    $title, 
                    $body, 
                    $data
                );
                $results[] = ['token' => $deviceToken->device_token, 'success' => true, 'message_id' => $result];
            } catch (\Exception $e) {
                $results[] = ['token' => $deviceToken->device_token, 'success' => false, 'error' => $e->getMessage()];
            }
        }
        return $results;
    }
    
    public function sendToMultipleDevices($deviceTokens, $title, $body, $data = [])
    {
        $results = [];
        
        foreach ($deviceTokens as $token) {
            try {
                $result = $this->sendToDevice($token, $title, $body, $data);
                $results[] = ['token' => $token, 'success' => true, 'message_id' => $result];
            } catch (\Exception $e) {
                $results[] = ['token' => $token, 'success' => false, 'error' => $e->getMessage()];
            }
        }
        
        return $results;
    }
}