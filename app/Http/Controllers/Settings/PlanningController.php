<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Planning;
use Illuminate\Http\Request;
use Auth;
use DB;
use CommonService;

class PlanningController extends Controller
{
    protected $db_table;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_planning';
        $this->db_table = new Planning;
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
    public function dataFields(){
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data["tables"] = DB::select('show columns from '.env("DB_PREFIX").($this->db_table)->getTable());
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
            $years = array(
                [
                    "label" => "2020",
                    "value" => "2020"
                ],
                [
                    "label" => "2021",
                    "value" => "2021"
                ],
                [
                    "label" => "2022",
                    "value" => "2022"
                ],
                [
                    "label" => "2023",
                    "value" => "2023"
                ],
                [
                    "label" => "2024",
                    "value" => "2024"
                ],
                [
                    "label" => "2025",
                    "value" => "2025"
                ],
                [
                    "label" => "2026",
                    "value" => "2026"
                ],
                [
                    "label" => "2027",
                    "value" => "2027"
                ]
            );
            $data = array(
                "data_fields" => $this->dataFields(),
                "year" => $years,
                "data" => $this->db_table::getPlannings($filter),
                "limit" => config_limit,
                "total" => $this->db_table::getCount($filter)
            );
            return response()->json($data, $status);
        } else {
            $status = 500;
            $data["is_display"] = false;
            $data["message"] = trans('common.msg_no_permission_access');
            return response()->json($data, $status);
        }
        
        // $input = $request->all();
        // $dataFields = $this->dataFields();
        // $filter = array(
        //     // "offset" => isset($input["offset"]) ? $input["offset"] : config_offset,
        //     "limit" => isset($input["limit"]) ? $input["limit"] : config_limit,
        //     "sort" => isset($input["sort"]) ? $input["sort"] : config_sort,
        //     "order" => isset($input["order"]) ? $input["order"] : config_order
        // );
        // $query = $this->db_table::orderByFilter($filter);
        // $whereClause = $query;
        // $whereClause->offset(($input["page_number"] - 1) * $filter["limit"]);       
        // $whereClause->limit($filter["limit"]);
        // $whereClause->where("status", null)->orWhere("status", 0);
        // if(isset($input["search_field"])){
        //     for($i=0 ; $i < count($input["search_field"]); $i++){
        //         $field = array_key_first($input["search_field"][$i]); //array('key1', 'key2', 'key3');
        //         if (in_array($field, $dataFields)) {
        //             $whereClause->orWhere($field, "Like","%".$input["search_field"][$i][$field]."%");
        //         }
        //     }
        // }
        
        // $table = collect($whereClause->get());
        // $years = array(
        //     [
        //         "label" => "2020",
        //         "value" => "2020"
        //     ],
        //     [
        //         "label" => "2021",
        //         "value" => "2021"
        //     ],
        //     [
        //         "label" => "2022",
        //         "value" => "2022"
        //     ],
        //     [
        //         "label" => "2023",
        //         "value" => "2023"
        //     ],
        //     [
        //         "label" => "2024",
        //         "value" => "2024"
        //     ],
        //     [
        //         "label" => "2025",
        //         "value" => "2025"
        //     ]
        // );
        // $data = array(
        //     "data_fields" => $this->dataFields(),
        //     "year" => $years,
        //     "data" => $table,
        //     "limit" => $filter["limit"],
        //     "total" =>collect($whereClause->count())
        // );
        // return response()->json($data);
    }

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
        $data = array(
            "success" => $boolen,
            "message" => $message
        );
        return response()->json($data, $status);
    }
}