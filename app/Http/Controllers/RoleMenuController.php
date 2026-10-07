<?php

namespace App\Http\Controllers;

use App\Models\Settings\RoleMenu;
use Illuminate\Http\Request;
use CommonService;

// The signed-in user's permissions, loaded by the web app at login and for the side menu
class RoleMenuController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $filter = CommonService::getFilter($request->all());
        $db_table = new RoleMenu;
        return response()->json([
            "data" => $db_table::getData($filter),
            "permissions" => CommonService::permission($db_table),
            "role_permissions" => CommonService::getPermissionsViaRoles(),
            "is_display" => true,
            "message" => trans('common.msg_successfully'),
        ], 200);
    }
}
