<?php

namespace App\Http\Controllers\Modules\ProgramManagement;

use App\Http\Controllers\Controller;
use App\Models\Modules\ProgramManagement\Program;
use App\Models\Modules\ProgramManagement\ClusterActivity;
use App\Models\Modules\ProgramManagement\Activity;
use App\Models\Modules\ProgramManagement\Task;
use App\Models\Settings\Entity;
use App\Models\Settings\AccountGroup;
use App\Models\Settings\Unit;
use Illuminate\Http\Request;
use Auth;
use DB;
use CommonService;

class TaskController extends Controller
{
    protected $db_table;
    public $path = "admin/modules/task";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->db_table = new Task;
        $this->lang_path = $this->path;
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
        // $input = $request->all();
        // $dataFields = $this->dataFields();
        // $filter = CommonService::getFilter($input);
        
        // $clusterActivities = ClusterActivity::getClusterActs($filter);
        // $activities = [];
        // $entities = [];
        // $entity_members = [];

        // $data = array(
        //     "data_fields" => $this->dataFields(),
        //     "data" => $this->db_table::getTaskByAct($filter),
        //     "program_id" => Program::getPrograms($filter),
        //     "sub_program_id" => [],//SubProgram::getSubProgramsByPro(),
        //     "cluster_activity_id" => [],//$clusterActivities,
        //     // "cluster_activity_id" => $clusterActivities,
        //     "activity_id" => $activities,
        //     "entity_id" => $entities,
        //     "entity_member_id" => $entity_members,
        //     "unit_id" => CommonService::optVals(Unit::class, $filter, $flag = 2),
        //     "limit" => config_limit,
        //     "total" => Activity::getCount($filter)
        // );
        // return response()->json($data);
        $input = $request->all();
        $dataFields = $this->dataFields();
        $filter = CommonService::getFilter($input);
        $clusterActivities = ClusterActivity::getClusterActs($filter);
        $entities = Entity::getSingleOpt();
        $entity_members = [];

        $dataHeaders = array(
            "header1" => array(
                "label" => "",
                "width:" =>0,
                "rowspa" => 0,
                "colspan" => 0,
            ),
            "header8" => array(
                "label" => "item_description",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            
            "header2" => array(
                "label" => "",
                "width:" =>0,
                "rowspa" => 0,
                "colspan" => 0,
            ),
            "header3" => array(
                "label" => "Line#",
                "width:" =>0,
                "rowspa" => 0,
                "colspan" => 0,
            ),
            "header4" => array(
                "label" => "",
                "width:" =>0,
                "rowspa" => 0,
                "colspan" => 0,
            ),
            "header5" => array(
                "label" => "Act.Cluster",
                "flag" => "y_n_minus_1",
                "rowspa" => 0,
                "colspa" => 0,
            ),
            "header6" => array(
                "label" => "Pro.Activity",
                "flag" => "y_n",
                "rowspa" => 0,
                "colspa" => 0,
            ),
            "header7" => array(
                "label" => "Sub-Account",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header9" => array(
                "label" => "is_reg_exp",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header10" => array(
                "label" => "unit",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header11" => array(
                "label" => "quantity",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header12" => array(
                "label" => "currency",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header13" => array(
                "label" => "cost_unit",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header14" => array(
                "label" => "cost_per_time", //តម្លៃ១លើក
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header15" => array(
                "label" => "per_annual", //លើក/ឆ្នាំ
                "flag" => "y_n_1",
                "rowspa" => 0,
                "colspa" => 0,
            ),
            "header16" => array(
                "label" => "total_annual", //សរុបប្រចាំឆ្នាំ
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header17" => array(
                "label" => "month",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header18" => array(
                "label" => "expense_type",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header19" => array(
                "label" => "Misc", //ផ្សេងៗ
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header20" => array(
                "label" => "Action", //ផ្សេងៗ
                "rowspan" => 0,
                "colspan" => 0,
            )
        );
        $dataSubHeaders = array(
            "header1" => array(
                "label" => "1",
                "width" => 0,
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header2" => array(
                "label" => "2",
                "width" => 0,
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header3" => array(
                "label" => "3",
                "width" => 0,
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header4" => array(
                "label" => "4",
                "width" => "200",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header5" => array(
                "label" => "5",
                "flag" => "y_n_minus_1",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header6" => array(
                "label" => "6",
                "flag" => "y_n",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header7" => array(
                "label" => "7",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header8" => array(
                "label" => "8",
                "// flag" => "y_n_1",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header9" => array(
                "label" => "9",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header10" => array(
                "label" => "10",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header11" => array(
                "label" => "11",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header12" => array(
                "label" => "12",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header13" => array(
                "label" => "13",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header14" => array(
                "label" => "14", //តម្លៃ១លើក
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header15" => array(
                "label" => "15", //លើក/ឆ្នាំ
                "flag" => "y_n_1",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header16" => array(
                "label" => "16", //សរុបប្រចាំឆ្នាំ
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header17" => array(
                "label" => "17", //
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header18" => array(
                "label" => "18", //
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header19" => array(
                "label" => "19", //ផ្សេងៗ
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header20" => array(
                "label" => "", //ផ្សេងៗ
                "rowspan" => 0,
                "colspan" => 0,
            )
        );
        $dataFillables = array(
            "field2" => "code",
            "field1" => "name",
            "field3" => "group_chapter",
            "field4" => "no",
            "field5" => "line",
            "field6" => "code_cluster_activity",
            "field7" => "code_activity",
            "field8" => "sub_account",
            "field9" => "is_reg_exp",
            "field10" => "unit",
            "field11" => "quantity",
            "field12" => "currency",
            "field13" => "unit_price",
            "field14" => "total_amount",
            "field15" => "time_year",
            "field16" => "annual_amount",
            "field17" => "month",
            "field18" => "expense_type",
            "field19" => "remark",
        );

        $data = array(
            "data" => $this->db_table::getTaskByAct($filter),
            "data_fields" => $this->dataFields(),
            "dataFillables" => $dataFillables,
            "dataHeaders" => $dataHeaders,
            "dataSubHeaders" => $dataSubHeaders,
            "program_id" => Program::getPrograms(""),
            "sub_program_id" => [],//SubProgram::getSubProgramsByPro(),
            "cluster_activity_id" => [],//$clusterActivities,
            "activity_id" => [], //Activity::getActivities($filter),
            "entity_id" => $entities,
            "entity_member_id" => [], //$entity_members,
            "limit" => config_limit,
            "total" => ClusterActivity::getCount($filter)
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
        $table = $this->db_table::find($id);
        $data = array(
            "data" => $table,
        );
        return response()->json($data);
    }

    function getTaskDetail(Request $request){
        $input = $request->all();
        $dataFields = $this->dataFields();
        $filter = CommonService::getFilter($input);
        // $clusterActivities = $input["data_info"]["cluster_activity_id"]?$input["data_info"]["cluster_activity_id"]:[];
        // $activities = $input["data_info"]["activity_id"]?$input["data_info"]["activity_id"]:[];
        $data = array(
            "data" => $this->db_table::getTaskDetail($input),
            "cluster_activity_id" => $input["data_info"]["cluster_activity_id"]?array($input["data_info"]["cluster_activity_id"]):[],
            "activity_id" => $input["data_info"]["activity_id"]?array($input["data_info"]["activity_id"]):[],
            "task_id" => $input["data_info"]["id"]?array($input["data_info"]["id"]):[],
            "account_group_id"=> AccountGroup::getAccGroupOpts($filter),
            "account_id"=> [],
            "sub_account_id"=> [],
            "unit_id" => CommonService::optVals(Unit::class, $filter, 2),
            "limit" => config_limit,
            "total" => 0//Activity::getCount($filter)
        );
        return response()->json($data);
    }

    function getTaskByAct(Request $request){
        $input = $request->all();
        $filter = CommonService::getFilter($input);
        $data = array(
            "data" => $this->db_table::getTskByAct($filter, $input),
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
        $dataFields = $this->dataForm($input);
        $table = $this->db_table::where('id', $id)->update($dataFields);
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
        $table = $this->db_table::where('id', $id)->update(["status" => 4]);
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