<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\FormSchema;
use App\Models\Settings\Planning;
use Illuminate\Http\Request;
use Auth;
use DB;
use CommonService;
use App\Models\Settings\Status;

class PlanningController extends Controller
{
    protected $db_table;

    public function __construct()
    {
        $this->middleware('auth');
        $this->db_table = new Planning;
        $this->PERMISSION_ACTION_LIST = "list_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_CREATE = "create_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_UPDATE = "update_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_DELETE = "delete_".$this->db_table->getTable();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dataFields(){
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from '.DB::getTablePrefix().($this->db_table)->getTable());
        $str = "";
        for($i=0; $i < count($data["tables"]); $i++){
            $str .= $data["tables"][$i]->Field.",";
        }
        $newArr = explode(",", $str);
        array_pop($newArr);
        $dataFields = $newArr;
        return $dataFields;
    }
    public function index(Request $request)
    {
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST] == true){
            $input = $request->all();
            $dataFields = $this->dataFields();
            $filter = CommonService::getFilter($input);
            // Years from 2020 to next year, so the list never needs editing for a new year
            $years = array_map(
                fn ($year) => ['label' => (string) $year, 'value' => (string) $year],
                range(2020, (int) date('Y') + 1)
            );
            $data = array(
                "data_fields" => $this->dataFields(),
                "year" => $years,
                "data" => $this->db_table::getPlannings($filter),
                "form_attributes" => FormSchema::fields($this->db_table->getTable()),
                "permissions" => CommonService::permission($this->db_table),
                "model_name" => $this->db_table->getTable(),
                "limit" => $filter["limit"],
                "total" => $this->db_table::getCount($filter),
                "is_display" => true,
            );
            $status = 200;
            return response()->json($data, $status);
        } else {
            $status = 500;
            $data["is_display"] = false;
            $data["message"] = trans('common.msg_no_permission_access');
            return response()->json($data, $status);
        }

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
        $table = $this->db_table::create($dataFields);
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
        $table = $table=$this->db_table::find($id);
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
        $checkActivePlan = $this->db_table::where("is_default", 1)->first();
        $query = $this->db_table::query()->update([
            'is_default' => 0,
        ]);
        if($query){
            $dataFields = $this->dataForm($input);
            $table = $this->db_table::find($id);
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
                "data" => $this->db_table::findOrFail($id)
            );
            return response()->json($data, $status);
        }
    }

    public function dataForm($input){
        $arr = $input;
        $push_array = array_merge(array(["created_by" => Auth::user()->id]));
        $arraySingle = array_merge($arr, $push_array);
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
        $table=$this->db_table::find($id);
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
