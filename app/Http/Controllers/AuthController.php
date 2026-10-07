<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use OTPService;
use App\User;
use App\Models\Mobile\OTP;
use App\Models\Mobile\UserDevice;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        if (!\CommonService::can('create_users')) {
            return response()->json(['success' => false, 'message' => __('common.msg_no_permission_access')], 403);
        }
        // $request->validate([
        //     'name' => 'required|string',
        //     'email' => 'required|string|email|unique:users',
        //     'password' => 'required|string|confirmed'
        // ]);
        $user = new User([
            'fullname' => $request->fullname,
            'username' => $request->username,
            'email' => $request->email,
            'latin' => $request->latin,
            'telephone_1' => $request->telephone_1,
            'telephone_2' => $request->telephone_2,
            'password' => bcrypt($request->password)
        ]);
        $user->save();
        if ($request->role) $user->syncRoles([$request->role]);
        return response()->json([
            'message' => __('common.user_created')
        ], 201);
    }
    // Signs a token for $user. Lifetime: AUTH_TOKEN_HOURS on the web, AUTH_TOKEN_REMEMBER_DAYS with
    // "remember me" and for the mobile app. The expiry is part of the token itself, so it is set before creating it.
    protected function issueToken($user, bool $remember)
    {
        Passport::personalAccessTokensExpireIn($remember
            ? now()->addDays(config('auth.token_lifetime.remember_days'))
            : now()->addHours(config('auth.token_lifetime.hours')));
        return $user->createToken('Personal Access Token');
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        // $request->validate([
        //     'email' => 'required|string|email',
        //     'password' => 'required|string',
        //     'remember_me' => 'boolean'
        // ]);
    
        // $validatedData = $request->validate([
        //     'email' => 'required|string|email',
        //     'password' => 'required|string',
        //     'remember_me' => 'boolean'
        // ]);

        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => __('common.wrong_credentials')
            ], 200);
        $user = $request->user();
        $tokenResult = $this->issueToken($user, $request->boolean('remember_me'));
        return response()->json([
            'accessToken' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'userData' => $request->user(),
            
        ]);
    }

    public function loginMobile(Request $request)
    {
        $input = $request->all();
        $credentials = array(
                            'username' => $input["username"],
                            'password' => $input['password']
                        );
        // $credentials = request(['username', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => __('common.unauthorized')
            ], 401);
        $user = $request->user();
        // register user device
        $this->registerDevice($input, $user->id);
        // Create User Token Access
        $tokenResult = $this->issueToken($user, true);
        $data = array(
            'accessToken' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'userData' => $request->user()
        );
        return response()->json([
            "data" => $data
        ]);
    }

    public function loginMobileOTP(Request $request)
    {
        $input = $request->all();
        $telephone = $input['telephone'] ?? null;

        // Same answer whether or not the number has an account, so the endpoint cannot be used to
        // find out who is a member. A code is only created for an existing account.
        $data = array(
            "success" => true,
            "message" => __('common.otp_sent')
        );
        if ($telephone && User::checkHasUser($telephone)) {
            $otp = OTPService::getOTP($input);
            // No SMS provider yet: test servers may return the code (OTP_EXPOSE_CODE=true), production never
            if (config('services.otp.expose_code') && !empty($otp["optCode"])) {
                $data["optCode"] = $otp["optCode"];
            }
        }
        return response()->json([
            "data" => $data
        ]);
    }

    public function VerifyMobileOTP(Request $request)
    {
        $input = $request->all();
        $checkOTP = OTPService::checkOTP($request);
        $data = array();
        if($checkOTP){
            // The code was valid for this phone: sign that account in (its password stays unchanged)
            $user = User::where('username', $input['telephone'])->first();
            if (!$user)
                return response()->json([
                    'message' => __('common.unauthorized')
                ], 401);
            Auth::setUser($user);
            // register user device
            $this->registerDeviceWithF2A($input, $user->id);
            // Create User Token Access
            $tokenResult = $this->issueToken($user, true);
            $data = array(
                'accessToken' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
                'userData' => $user
            );
            return response()->json([
                "data" => $data
            ]);
        }else{
            $data = array(        
                "success" => false,
                "message" => __('common.otp_invalid')
            );
        }
        return response()->json([
            "data" => $data
        ]);
    }

    public function registerDeviceWithF2A($request, $userId)
    {
        $data = array();
        $userDevice = array(
            "user_id" => $userId,
            "telephone" => $request["telephone"],
            "optCode" => $request["optCode"],
            "device_type" => $request["device_type"],
            "device_token" => $request["device_token"]
        );
        if(!UserDevice::CheckUserDevice($request)){
            $table = UserDevice::create($userDevice);
            if($table){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_save_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
            $data = array(
                "success" => $boolen,
                "message" => $message
            );
        }else{
            $status = 200;
            $data = array(
                "success" => true,
                "message" => __('common.account_exists')
            );
        }
        
        return response()->json([
            "data" => $data
        ], $status);
    }
    public function registerDevice($request, $userId)
    {
        $data = array();
        $userDevice = array(
            "user_id" => $userId,
            // "telephone" => $request["telephone"],
            // "optCode" => $request["optCode"],
            "device_type" => $request["device_type"],
            "device_token" => $request["device_token"]
        );
        if(!UserDevice::CheckUserDevice($request)){
            $table = UserDevice::create($userDevice);
            if($table){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_save_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
            $data = array(
                "success" => $boolen,
                "message" => $message
            );
        }else{
            $status = 200;
            $data = array(
                "success" => true,
                "message" => __('common.account_exists')
            );
        }
        
        return response()->json([
            "data" => $data
        ], $status);
    }
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $data = array(
            "message" => __('common.logged_out'),
            "success" => true
        );
        return response()->json([
            'data' => $data
        ]);
    }
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($json);
    }
}