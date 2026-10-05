<?php

require_once 'vendor/autoload.php'; // Composer autoload for Google Auth Library

use Google\Auth\Credentials\ServiceAccountCredentials;

const SERVICE_ACCOUNT_KEY_PATH = './service-account-key.json';
const FCM_SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

function generateAccessToken() {
    try {
        // Check if the service account key file exists
        if (!file_exists(SERVICE_ACCOUNT_KEY_PATH)) {
            echo "Error: Service account key file not found at " . SERVICE_ACCOUNT_KEY_PATH . "\n";
            echo "Please download your service account JSON file from Firebase Console and place it in the correct location.\n";
            return null;
        }

        // Read the service account key file
        $serviceAccountJson = file_get_contents(SERVICE_ACCOUNT_KEY_PATH);
        $serviceAccountData = json_decode($serviceAccountJson, true);

        if (!$serviceAccountData) {
            echo "Error: Could not parse service account key file.\n";
            return null;
        }

        echo "Authenticating and requesting access token...\n";

        // Create credentials
        $credentials = new ServiceAccountCredentials(
            FCM_SCOPE,
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

function sendFCMNotification() {
    $YOUR_FIREBASE_PROJECT_ID = 'shm-app-ccb29'; // Your Firebase Project ID
    $TARGET_DEVICE_TOKEN = 'dQpc-ZSMQVOFXBw7ZMZZDW:APA91bFFp2yt8I7ZDhWB9sjs7xsgoH22Va78cs2VUx_racZ9JP4i7h7s8At2PNXGH0lwRSOFUntptc_7RPqfkmBD-OySxfqpVbNfSy0ENAUz3qFNwwV54nk';
    $API_ENDPOINT = "https://fcm.googleapis.com/v1/projects/{$YOUR_FIREBASE_PROJECT_ID}/messages:send";

    echo "Generating access token...\n";
    $accessToken = generateAccessToken();

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

// Execute the function
sendFCMNotification();

?>