<?php

namespace App\Http\Controllers\Modules\ProgramManagement;

use App\Http\Controllers\Controller;
use App\Models\Modules\ProgramManagement\Program;
use App\Models\Modules\ProgramManagement\Objective;
use App\Models\Settings\Entity;
use App\Models\Settings\EntityMember;
use Illuminate\Http\Request;
use CommonService;
use Auth;
use DB;

class ProgramController extends Controller
{
    protected $db_table;
    public $path = "admin/modules/program";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->db_table = new Program;
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
        $input = $request->all();
        $dataFields = $this->dataFields();
        $filter = CommonService::getFilter($input);

        $objectives = Objective::getObjectives();
        $entities = Entity::getSingleOpt();
        $entity_members = EntityMember::getMembers();
        $programs = Program::getPrograms("");

        $data = array(
            "data_fields" => $this->dataFields(),
            "data" => $this->db_table::getProgramByObj($filter),
            "objective_id" => $objectives,
            "program_id" => $programs,
            "entity_id" => $entities,
            "entity_member_id" => [],
            "limit" => $filter["limit"],
            "total" => Objective::getCount($filter)
        );
        return response()->json($data);

        // $query = $this->db_table::orderByFilter($filter);
        // $whereClause = $query;
        // $whereClause->offset(($input["page_number"] - 1) * $filter["limit"]);       
        // $whereClause->limit($filter["limit"]);
        // if(isset($input["search_field"])){
        //     for($i=0 ; $i < count($input["search_field"]); $i++){
        //         $field = array_key_first($input["search_field"][$i]); //array('key1', 'key2', 'key3');
        //         if (in_array($field, $dataFields)) {
        //             $whereClause->orWhere($field, "Like","%".$input["search_field"][$i][$field]."%");
        //         }
        //     }
        // }
        // $table = collect($whereClause->get());
    }

    public function getProgByObj(Request $request){
        $paramId = $request->all();
        $data = array(
            "data" => $this->db_table::getPrograms($paramId),
        );
        return response()->json($data);
    }

    public function _index(Request $request)
    {
        $input = $request->all();
        $dataFields = $this->dataFields();
        $filter = CommonService::getFilter($input);

        $programs = Objective::getPrograms();
        
        $data = array(
            "data_fields" => $this->dataFields(),
            "data" => $this->db_table::getSubProgramByProgram($filter),
            "program_id" => $programs,
            "entity_id" => $entities,
            "entity_member_id" => $entity_members,
            "limit" => $filter["limit"],
            "total" => Program::getCount($filter)
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
        // $input["code"] = "001";
        // $input["created_by"] = Auth::user()->id;    
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
        $dataFields = $this->dataForm($input);
        // return false;
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