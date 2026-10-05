<?php

namespace App\Http\Controllers\Modules\BudgetArrangement;

use App\Http\Controllers\Controller;
use App\Models\Modules\BudgetArrangement\PIPInvestment;
use App\Models\Modules\ProgramManagement\Program;
use App\Models\Modules\ProgramManagement\SubProgram;
use App\Models\Settings\ProjectType;
use App\Models\Settings\ProjectStatus;
use App\Models\Settings\FinanceResource;
use App\Models\Settings\Entity;
use Illuminate\Http\Request;
use Auth;
use DB;
use CommonService;

class PIPInvestmentController extends Controller
{
    protected $db_table;
    public $path = "admin/modules/ceiling_rule";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->db_table = new PIPInvestment;
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
        $dataHeaders = array(
            "header1" => array(
                "label" =>  "pip_year_2023",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header2" => array(
                "label" =>  "pip_year_2024",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header3" => array(
                "label" =>  "pip_year_2025",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header4" => array(
                "label" =>  "pip_total_year",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header5" => array(
                "label" =>  "pip_year_2023",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header6" => array(
                "label" =>  "pip_year_2024",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header7" => array(
                "label" =>  "pip_year_2025",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header8" => array(
                "label" =>  "pip_total_year",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header9" => array(
                "label" =>  "pip_year_2023",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header10" => array(
                "label" =>  "pip_year_2024",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header11" => array(
                "label" =>  "pip_year_2025",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header12" => array(
                "label" =>  "pip_total_year",
                "rowspan" => 0,
                "colspan" => 0,
            ),
        );
        $dataSubHeaders = array(
            "header1"=> array(
                "label" => "1",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header2"=> array(
                "label" => "2",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header3"=> array(
                "label" => "3",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header4"=> array(
                "label" => "4",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header5"=> array(
                "label" => "5",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header6"=> array(
                "label" => "6",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header7"=> array(
                "label" => "7",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header8"=> array(
                "label" => "8",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header9"=> array(
                "label" => "9",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header10"=> array(
                "label" => "10",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header11"=> array(
                "label" => "11",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header12"=> array(
                "label" => "12",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header13"=> array(
                "label" => "13",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header14"=> array(
                "label" => "14",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header15"=> array(
                "label" => "15",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header16"=> array(
                "label" => "16",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header17"=> array(
                "label" => "17",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header18"=> array(
                "label" => "18",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header19"=> array(
                "label" => "19",
                "rowspan" => 0,
                "colspan" => 0,
            ),
        );        

        $data = array(
            "data_fields" => $this->dataFields(),
            "data" => $this->db_table::getPIPInvestment($filter),
            "dataHeaders" => $dataHeaders,
            "dataSubHeaders" => $dataSubHeaders,
            "program_id" => Program::getPrograms(""),
            "sub_program_id" => [],//SubProgram::getSubProgramsByPro(),
            "entity_id" => [],//Entity::getBySubProgEntities($filter),
            "project_type_id" => ProjectType::getProjectTypeOpts(),
            "project_status_id" => ProjectStatus::getProjectStatusOpt(),
            "finance_resource_id" => FinanceResource::getFinanceResourceOpts(),
            "limit" => config_limit,
            "total" => $this->db_table::count()
        );
        return response()->json($data);
    }

    public function getPipInfo(Request $request){
        $progId = $input["program_id"];
        $subProId = $input["sub_program_id"];
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