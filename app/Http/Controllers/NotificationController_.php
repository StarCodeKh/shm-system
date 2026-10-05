<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use App\Models\NotificationType;
use App\Models\Mobile\UserDevice;
use PushNotificationService;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\User;
use DB;
use Auth;
use DateTime;
use CommonService;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Client;
class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    protected $PERMISSION_ACTION_LIST;
    protected $PERMISSION_ACTION_CREATE;
    protected $PERMISSION_ACTION_SHOW;
    protected $PERMISSION_ACTION_EDIT;
    protected $PERMISSION_ACTION_UPDATE;
    protected $PERMISSION_ACTION_DELETE;
    protected $db_table;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->db_table = new Notification;
        $this->lang_path = $this->path;
         $this->PERMISSION_ACTION_LIST = "list_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_CREATE = "create_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_SHOW = "show_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_EDIT = "edit_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_UPDATE = "update_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_DELETE = "delete_".$this->db_table->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index_(Request $request)
    // {
    //     $data = array();
    //     if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST] == true){
    //         $input = $request->all();
    //         $filter = CommonService::getFilter($input);
    //         $data = array(
    //             "data" => $this->db_table::getData($filter),
    //             "limit" => config_limit,
    //             "total" => $this->db_table::getTotal($filter),
    //             "permissions" => CommonService::permission($this->db_table),
    //             "role_permissions" => CommonService::getPermissionsViaRoles(),
    //             "model_name" => $this->db_table->getTable(),
    //             "data_fields" => $this->db_table::dataFields(),
    //             "form_attributes" => $this->db_table::formAttributes(),
    //             "notification_type_id" => NotificationType::getSingleOpt(),
    //         );
    //         $status = 200;
    //         $boolen = true;
    //         $data["message"] = trans('common.msg_save_successfully');
    //         return response()->json($data, $status);
    //     }else{
    //         $status = 500;
    //         $boolen = false;
    //         $data["message"] = trans('common.msg_no_permission_access');
    //         return response()->json($data, $status);
    //     }
    // }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_CREATE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_CREATE] == true){
            $input = $request->all();
            $dataFields = $this->dataForm($input);
            $data["data"] = $this->db_table::create($dataFields);
            if($data["data"]){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_save_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $data,
        );
        return response()->json($data, $status);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW] == true){
            $query = $this->db_table::find($id);
            if($query){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_successfully');
                $data["data"] = $query;
                $data["entity_id"] = Entity::getSingleOpt();
                $data["position"] = Position::getSingleOpt();
                $data["title"] = Title::getSingleOpt();
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $data,
        );
        return response()->json($data, $status);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE] == true){
            $input = $request->all();
            $dataFields = $this->dataForm($input);
            $table = $table=$this->db_table::find($id);
            $table->update($dataFields);
            if($table){
                $data = $this->db_table::findOrFail($id);
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_update_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $data
        );
        return response()->json($data, $status);
    }

    public function dataForm($input){
        $arr = $input;
        $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
        $arraySingle = array_merge($arr, $pushArray);
        $result = call_user_func_array('array_merge', $arraySingle);
        $dataFields = $result;
        return $dataFields;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_DELETE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_DELETE] == true){
            $table=$this->db_table::find($id);
            $table->update(["status" => 4]);
            if($table){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_delete_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
            
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message
        );
        return response()->json($data, $status);
    }

    public function sendNotification(Request $request){
        $sendPushNotification = PushNotificationService::sentNotificationToUserDevice($request);
        $data = array();
        if($sendPushNotification){
            $queryUserDevices = UserDevice::GroupBy("user_id")->get();
            foreach($queryUserDevices as $userDevice){
                // ########## Record All Users Notification
                // NotificationUser::create([
                //     "user_id" => $userDevice->user_id,
                //     "officer_id" => "",
                //     "title" => $request["title"],
                //     "description" => $request["body"],
                //     "status" => 1,
                //     "push_count" => 1,
                //     "notification_type" => $request["notification_type"],
                //     "created_at" => date("Y-m-d H:i:s")
                // ]);
            }
            $data = array(
                "success" => true,
                "message" => "Notification has been sent."
            );
        }else{
            $data = array(
                "success" => false,
                "message" => "Fail to send notification!"
            );
        }
        return response()->json($data);
    }
   
    public static function getUnreadNotifications(){
        return response()->json([
            'unread_count' => auth()->user()->unreadNotifications->count(),
            'unread_notifications' => auth()->user()->unreadNotifications
        ]);
    }

    // Notification new function
    public function index() {        
        $YOUR_FIREBASE_PROJECT_ID = 'shm-app-ccb29'; // Your Firebase Project ID
        $TARGET_DEVICE_TOKEN = 'dQpc-ZSMQVOFXBw7ZMZZDW:APA91bFFp2yt8I7ZDhWB9sjs7xsgoH22Va78cs2VUx_racZ9JP4i7h7s8At2PNXGH0lwRSOFUntptc_7RPqfkmBD-OySxfqpVbNfSy0ENAUz3qFNwwV54nk';
        $API_ENDPOINT = "https://fcm.googleapis.com/v1/projects/{$YOUR_FIREBASE_PROJECT_ID}/messages:send";        
        //echo "Generating access token...\n";
        $accessToken = $this->generateAccessToken();
        return $accessToken;
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

    
    public function generateAccessToken() {
        try {
            $SERVICE_ACCOUNT_KEY_PATH = base_path("service-account-key.json");   
            //$SERVICE_ACCOUNT_KEY_PATH = "./service-account-key.json";            
            $FCM_SCOPE = "https://www.googleapis.com/auth/firebase.messaging";
            // Check if the service account key file exists
            if (!file_exists($SERVICE_ACCOUNT_KEY_PATH)) {
                echo "Error: Service account key file not found at " . $SERVICE_ACCOUNT_KEY_PATH . "\n";
                echo "Please download your service account JSON file from Firebase Console and place it in the correct location.\n";
                return null;
            }

            // Read the service account key file
            $serviceAccountJson = file_get_contents($SERVICE_ACCOUNT_KEY_PATH);
            $serviceAccountData = json_decode($serviceAccountJson, true);
            //return $serviceAccountData;
            if (!$serviceAccountData) {
                echo "Error: Could not parse service account key file.\n";
                return null;
            }

            echo "Authenticating and requesting access token...\n";

            // Create credentials
            $credentials = new ServiceAccountCredentials(
                $FCM_SCOPE,
                $serviceAccountData
            );
            return $credentials;

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

}
