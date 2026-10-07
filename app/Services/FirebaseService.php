<?php

// app/Services/FirebaseService.php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Contract\Messaging;

class FirebaseService
{
    protected $auth;
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $this->auth = $factory->createAuth();
        $this->messaging = $factory->createMessaging();
    }

    public function getAuth(): Auth
    {
        return $this->auth;
    }

    public function getMessaging(): Messaging
    {
        return $this->messaging;
    }
}
