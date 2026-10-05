<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class GoogleAuthController extends Controller
{
    protected $firebaseAuth;
    
    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }
    
    public function loginWithGoogle(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string'
        ]);
        
        try {
            // Verify ID token with Firebase
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($request->id_token);
            $uid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');
            $name = $verifiedIdToken->claims()->get('name');
            $picture = $verifiedIdToken->claims()->get('picture');
            
            // Find or create user
            $user = User::firstOrCreate(
                ['firebase_uid' => $uid],
                [
                    'name' => $name,
                    'email' => $email,
                    'avatar' => $picture
                ]
            );
            
            // Login user
            Auth::login($user);
            
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $user->createToken('auth-token')->plainTextToken
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed'
            ], 401);
        }
    }
    
    public function registerDeviceToken(Request $request)
    {
        $request->validate([
            'device_token' => 'required|string',
            'platform' => 'nullable|in:android,ios'
        ]);
        
        $user = Auth::user();
        
        // Upsert device token
        $user->deviceTokens()->updateOrCreate(
            [
                'device_token' => $request->device_token,
                'user_id' => $user->id
            ],
            [
                'platform' => $request->platform ?? 'android'
            ]
        );
        
        return response()->json(['success' => true]);
    }
}