<?php

namespace App\Http\Controllers\Modules\BudgetArrangement;

use App\Http\Controllers\Controller;
use App\Models\Modules\BudgetArrangement\CeilingEntity;
use App\Models\Settings\CeilingGroup;
use App\Models\Settings\CeilingType;
use App\Models\Modules\ProgramManagement\Program;
use App\Models\Modules\ProgramManagement\SubProgram;
use Illuminate\Http\Request;
use Auth;
use DB;
use CommonService;

class CeilingEntityController extends Controller
{
    protected $db_table;
    public $path = "admin/modules/ceiling_entity";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->db_table = new CeilingEntity;
        $this->lang_path = $this->path;
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
        $input = $request->all();
        $dataFields = $this->dataFields();
        $filter = CommonService::getFilter($input);
        
        $programs = Program::getPrograms("");
        $subPrograms = [];
        $entities = [];
        // $entity_members = array(
        //     [
        //         "label" => "សមាជិកទី១",
        //         "value" => 1,
        //     ],
        //     [
        //         "label" => "សមាជិកទី២",
        //         "value" => 2,
        //     ]
        // );

        $dataHeaders = array(
            // "header1" => array(
            //     "label" => "២០២៣",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header2" => array(
            //     "label" => "២០២៤",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header3" => array(
            //     "label" => "២០២៥",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header4" => array(
            //     "label" => "២០២៣",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header5" => array(
            //     "label" => "២០២៤",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header6" => array(
            //     "label" => "២០២៥",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // )
            // header7: {
            //     label: "",
            //     rowspan: 0,
            //     colspan: 0,
            // }
            );
        $dataSubHeaders = array(
            "header1" => array(
                "label" => "1",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header2" => array(
                "label" => "2",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header3" => array(
                "label" => "3",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header4" => array(
                "label" => "4",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            // "header5" => array(
            //     "label" => "5",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header6" => array(
            //     "label" => "6",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header7" => array(
            //     "label" => "7=(4-1)/1",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header8" => array(
            //     "label" => "8=(5-4)/4",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header9" => array(
            //     "label" => "9=(6-5)/5",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
            // "header10" => array(
            //     "label" => "10",
            //     "rowspan" => 0,
            //     "colspan" => 0,
            // ),
        );
            // header11: {
            //     label: "",
            //     rowspan: 0,
            //     colspan: 0,
            // },
        $data = array(
            "data" => CeilingEntity::getCeilingEntities($filter),
            "data_fields" => $this->dataFields(),
            "dataHeaders" => $dataHeaders,
            "dataSubHeaders" => $dataSubHeaders,
            "program_id" => $programs,
            "sub_program_id" => [],
            "entity_id" => $entities,
            "ceiling_exp_group" => CeilingGroup::getCeilingGroupsOptions($filter),
            "ceiling_exp_type" => CeilingType::getCeilingTypeOptions($filter),
            "ceiling_rule" => [],
            "irregular_expense" => [],
            "target_type_id" => [],
            "limit" => config_limit,
            "total" => $this->db_table::count()
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
        $push_array = array_merge(array(["planning_id" => config_planning_year, "created_by" => Auth::user()->id]));
        $arraySingle = array_merge($arr, $push_array);
        $result = call_user_func_array("array_merge",$arraySingle);
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