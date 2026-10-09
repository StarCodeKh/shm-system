<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Audit;
use Illuminate\Http\Request;
use CommonService;
use App\Models\Settings\Status;
class AuditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $db_table;

    public function __construct()
    {
        $this->middleware('auth');
        $this->dbTable = new Audit;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!CommonService::can('audit')) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        $input = $request->all();
        $filter = CommonService::getFilter($input);
        $data = array(
            "data" => $this->dbTable::getData($filter),
            "limit" => $filter["limit"],
            "total" => $this->dbTable::getTotal($filter),
            "data_fields" => $this->dbTable::dataFields(),
            // Record types for the filter (only on the first page load)
            "types" => empty($input["search_field"]) && (int) ($filter["page_number"] ?? 1) <= 1
                ? $this->dbTable::query()->select("auditable_type")->distinct()->orderBy("auditable_type")->pluck("auditable_type")
                : null,
        );
        return response()->json($data);
    }

    /**
     * Clear old log rows: days = 30 / 90 / 180 / 365, or 0 for every row.
     *
     * @return \Illuminate\Http\Response
     */
    public function clear(Request $request)
    {
        if (!CommonService::can('audit')) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        $days = (int) $request->validate(['days' => 'required|integer|in:0,30,90,180,365'])['days'];
        $deleted = $this->dbTable::clearOlderThan($days, $request);
        return response()->json([
            "success" => true,
            "deleted" => $deleted,
            "message" => trans('common.msg_delete_successfully'),
        ]);
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
        $dataFields = $this->dataForm($input);
        $table = $this->dbTable::create($dataFields);
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
        $table = $table=$this->dbTable::find($id);
        $data = array(
            "data" => $table
        );
        return response()->json($data);
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
        $dataFields = $this->dataForm($input);
        $table = $table=$this->dbTable::find($id);
        $table->update($dataFields);
        if($table){
            $status = 200;
            $boolen = true;
            $message = trans('common.msg_update_successfully');
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.error_msg');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $this->dbTable::findOrFail($id)
        );
        return response()->json($data, $status);
    }

    public function dataForm($input){
        $arr = $input;
        $pushArray = array_merge(array([]));
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
        $table=$this->dbTable::find($id);
        $table->update(["status" => Status::DELETED]);
        if($table){
            $status = 200;
            $boolen = true;
            $message = trans('common.msg_delete_successfully');
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.error_msg');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message
        );
        return response()->json($data, $status);
    }
}
