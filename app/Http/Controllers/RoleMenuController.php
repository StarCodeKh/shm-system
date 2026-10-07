<?php

namespace App\Http\Controllers;

use CommonService;

// The signed-in user's permissions ({ name: true }), loaded by the web app at login and for the side menu
class RoleMenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return response()->json([
            "permissions" => CommonService::permission(),
            "is_display" => true,
            "message" => trans('common.msg_successfully'),
        ], 200);
    }
}
