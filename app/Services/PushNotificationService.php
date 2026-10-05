<?php 	
	
	namespace App\Services;
	use App\Models\Mobile\UserDevice;
	use App\Models\Mobile\OTP;
	use Carbon\Carbon;
	use DB;
	use Validator;
	use Auth;
	use Session;
	use Kreait\Firebase\Messaging\CloudMessage;
	use Kreait\Firebase\Messaging\Notification;
	use App\User;
	use Google\Auth\Credentials\ServiceAccountCredentials;
	use Kreait\Firebase\Factory;
	// use App\Models\Notification;
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
								->where("u.status", 13)
								->where("receive_all_notification", 1)
								->get();
			$deviceTokens = [];
			foreach($queryUserDevices as $userDevice){
				$deviceTokens[] = array(
					"user_id" => $userDevice->id,
					"device_token" => $userDevice->device_token,
				);
			}
			// dd($deviceTokens);
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

		public static function generateAccessToken() {
			try {
				// Check if the service account key file exists
				if (!file_exists(env('SERVICE_ACCOUNT_KEY_PATH'))) {
					echo "Error: Service account key file not found at " . env('SERVICE_ACCOUNT_KEY_PATH') . "\n";
					echo "Please download your service account JSON file from Firebase Console and place it in the correct location.\n";
					return null;
				}

				// Read the service account key file
				$serviceAccountJson = file_get_contents(env('SERVICE_ACCOUNT_KEY_PATH'));
				$serviceAccountData = json_decode($serviceAccountJson, true);

				if (!$serviceAccountData) {
					echo "Error: Could not parse service account key file.\n";
					return null;
				}

				echo "Authenticating and requesting access token...\n";

				// Create credentials
				$credentials = new ServiceAccountCredentials(
					env('FCM_SCOPE'),
					$serviceAccountData
				);

				// Fetch the access token
				$authToken = $credentials->fetchAuthToken();

				if (isset($authToken['access_token'])) {
					echo "Successfully generated FCM Access Token!\n";
					echo "Token: " . $authToken['access_token'] . "\n";
					if (isset($authToken['expires_in'])) {
						echo "Expires in: " . $authToken['expires_in'] . " seconds\n";
					}
					return $authToken['access_token'];
				} else {
					echo "Failed to get access token from response.\n";
					return null;
				}

			} catch (Exception $e) {
				echo "An error occurred while generating the access token: " . $e->getMessage() . "\n";
				return null;
			}
		}


		public static function sentNotificationToUserDevice($request){
			$YOUR_FIREBASE_PROJECT_ID = 'shm-app-ccb29'; // Your Firebase Project ID
			$TARGET_DEVICE_TOKEN = 'cOKem48iQ9qKnrViXj-M2Z:APA91bGiwaU0_fmc8gwf0bnuneb4pxauJExLJh8_AUi1qUVvNg4ncgHd2NWR_ZYMHpj71BF5XJ3OMO9NwJfDCGc97ahetPFE5QHRNuoLFaxVLsH2-TstT5I';
			$API_ENDPOINT = "https://fcm.googleapis.com/v1/projects/{$YOUR_FIREBASE_PROJECT_ID}/messages:send";

			echo "Generating access token...\n";
			$accessToken = PushNotificationService::generateAccessToken();

			if (!$accessToken) {
				echo "Failed to obtain an access token. Cannot send notification.\n";
				return;
			}
			echo "Access token obtained.\n";

			$messagePayload = [
				'message' => [
					'token' => $TARGET_DEVICE_TOKEN,
					'notification' => [
						'title' => "Hello from your PHP App!",
						'body' => "This is a push notification sent via FCM v1 API and PHP.",
					],
					'data' => [
						'key1' => "value1",
						'event' => "new_message",
						'senderId' => "GeminiInFirebase",
					],
					'android' => [
						'priority' => "high",
						'notification' => [
							'icon' => "ic_launcher",
							'color' => "#f45342",
						],
					],
					'apns' => [
						'payload' => [
							'aps' => [
								'sound' => "default",
							],
						],
					],
				],
			];

			try {
				echo "Sending FCM notification...\n";

				// Initialize cURL
				$ch = curl_init($API_ENDPOINT);
				
				// 	curl_setopt($ch, CURLOPT_URL, config_FIRE_BASE_SERVER_URL);
				// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				// curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, [
					'Content-Type: application/json',
					'Authorization: Bearer ' . $accessToken,
				]);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messagePayload));

				// Execute request
				$response = curl_exec($ch);
				$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$curlError = curl_error($ch);
				curl_close($ch);

				if ($curlError) {
					echo "cURL Error: " . $curlError . "\n";
					return;
				}

				$responseData = json_decode($response, true);

				if ($httpCode >= 200 && $httpCode < 300) {
					echo "Successfully sent message!\n";
					echo "Response: " . print_r($responseData, true) . "\n";
					if (isset($responseData['name'])) {
						echo "Message ID: " . $responseData['name'] . "\n";
					}
				} else {
					echo "Error sending message: HTTP " . $httpCode . "\n";
					echo "Error details: " . print_r($responseData, true) . "\n";
				}

			} catch (Exception $e) {
				echo "An error occurred during the HTTP request: " . $e->getMessage() . "\n";
			}
		}
	}
?>