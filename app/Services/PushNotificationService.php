<?php

    namespace App\Services;
    use DB;
use Illuminate\Support\Facades\Log;
use App\Models\Settings\Status;
    use Google\Auth\Credentials\ServiceAccountCredentials;
    use Kreait\Firebase\Factory;
    class PushNotificationService{

        protected $messaging;

        public function __construct(FirebaseService $firebase)
        {
            $this->messaging = $firebase->getMessaging();
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

        public static function sendToDevice(string $deviceToken, string $title, string $message, $data = [])
        {
            $firebase = (new Factory)->withServiceAccount(storage_path('firebase/firebase_credentials.json'));
            $messaging = $firebase->createMessaging();
            $message = \Kreait\Firebase\Messaging\CloudMessage::new()->withToken($deviceToken)
                ->withNotification([
                    'title' => $title,
                    'body' => $message,
                ]);
            $messaging->send($message);
        }

        public static function sendToMultipleDevices($title, $message, $type, $data = [])
        {
            // Push Notification to user which has the role to get notify
            $queryUserDevices = DB::table("users as u")
                ->select("u.id", "ud.device_token")
                ->Join("user_device as ud", "ud.user_id", "=", "u.id")
                ->where("u.status", Status::ACTIVE)
                ->where("receive_all_notification", 1)
                ->get();
            $deviceTokens = [];
            foreach($queryUserDevices as $userDevice){
                $deviceTokens[] = array(
                    "user_id" => $userDevice->id,
                    "device_token" => $userDevice->device_token,
                );
            }
            $results = [];
            foreach ($deviceTokens as $token) {
                try {
                    $result = PushNotificationService::sendToDevice($token["device_token"], $title, $message, $data);
                    DB::table("notifications")->insert([
                        "notifiable_id" => $token["user_id"],
                        "type" => $type,
                        "subject" => $title,
                        "message" => $message,
                        "notifiable_type" => "App\User",
                        "created_at" => date("Y-m-d H:i:s")
                    ]);
                    $results[] = ['token' => $token["device_token"], 'success' => true, 'message_id' => $result];
                } catch (\Exception $e) {
                    $results[] = ['token' => $token["device_token"], 'success' => false, 'error' => $e->getMessage()];
                }
            }
            return $results;
        }

        // OAuth access token for the FCM v1 API, from the service account file (config services.fcm).
        // Problems go to the Laravel log; the token itself is never logged or printed.
        public static function generateAccessToken() {
            $file = config('services.fcm.credentials');
            try {
                if (!$file || !file_exists($file)) {
                    Log::error('FCM: service account key file not found', ['path' => $file]);
                    return null;
                }

                $serviceAccountData = json_decode(file_get_contents($file), true);
                if (!$serviceAccountData) {
                    Log::error('FCM: could not parse the service account key file', ['path' => $file]);
                    return null;
                }

                $credentials = new ServiceAccountCredentials(config('services.fcm.scope'), $serviceAccountData);
                $authToken = $credentials->fetchAuthToken();

                if (empty($authToken['access_token'])) {
                    Log::error('FCM: no access token in the auth response');
                    return null;
                }
                return $authToken['access_token'];
            } catch (\Throwable $e) {
                Log::error('FCM: access token request failed', ['error' => $e->getMessage()]);
                return null;
            }
        }
    }
?>
