<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationType;
use Auth;
use DB;
use CommonService;
class NotificationTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $db_table;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->dbTable = new NotificationType;
        $this->langPath = $this->path;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $filter = CommonService::getFilter($input);
        $data = array(
            "data" => $this->dbTable::getData($filter),
            "limit" => config_limit,
            "total" => $this->dbTable::getTotal($filter),
            "data_fields" => $this->dbTable::dataFields(),
            "form_attributes" => $this->dbTable::formAttributes()
        );
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $table = NotificationType::create($input);
        if($table){
            $status = 200;
            $boolen = true;
            $message = trans('notification_type.message_success');
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('notification_type.message_error');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $table,
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
        $table = NotificationType::find($id);
        $data = array(
            "data" => $table
        );
        return response()->json($data);
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
        $input = $request->all();
        $table = NotificationType::where('id', $id)->update($input);
        if($table){
            $status = 200;
            $boolen = true;
            $message = trans('notification_type.message_update');
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('notification_type.message_error');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message
        );
        return response()->json($data, $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $table = NotificationType::where('id', $id)->update(["status" => 4]);
        if($table){
            $status = 200;
            $boolen = true;
            $message = trans('notification_type.message_delete');
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('notification_type.message_error');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message
        );
        return response()->json($data, $status);
    }
}
