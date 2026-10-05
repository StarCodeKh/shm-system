<?php

namespace App\Http\Controllers\Modules\BudgetMonitoring;
use App\Models\Settings\Entity;
use App\Http\Controllers\Controller;
use App\Models\Modules\BudgetMonitoring\RevProgram;
use App\Models\Settings\AccountGroup;
use Illuminate\Http\Request;
use Auth;
use DB;
use CommonService;

class RevProgramController extends Controller
{
    protected $db_table;
    public $path = "admin/modules/entry_rev_program";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->db_table = new RevProgram;
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
        $entities = Entity::getSingleOpt();
        $dataHeaders = array(
            "header1" => array(
                "label" => "គ្រោងដើមឆ្នាំ",
                "rowspan" => 0,
                "colspan" => 4
            ),
            "header2" => array(
                "label" => "ប៉ាន់ស្មានអនុវត្ត",
                "rowspan" => 0,
                "colspan" => 4
            ),
            "header3" => array(
                "label" => "គ្រោងដើមឆ្នាំ",
                "rowspan" => 0,
                "colspan" => 4
            ),
            "header4" => array(
                "label" => "ប៉ាន់ស្មានអនុវត្ត",
                "rowspan" => 0,
                "colspan" => 4
            ),
            "header5" => array(
                "label" => "គ្រោងដើមឆ្នាំ",
                "rowspan" =>  0,
                "colspan" => 4
            ),
            "header6" => array(
                "label" => "ប៉ាន់ស្មានអនុវត្ត",
                "rowspan" =>  0,
                "colspan" => 4
            ),
            "header7" => array(
                "label" => "គ្រោងដើមឆ្នាំ",
                "rowspan" =>  0,
                "colspan" => 4
            ),
            "header8" => array(
                "label" => "ប៉ាន់ស្មានអនុវត្ត",
                "rowspan" =>  0,
                "colspan" => 4
            )
        );

        $dataSubHeaders = array(
            "header1" => array(
                "label" => "ទឹកប្រាក់",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header2" => array(
                "label" => "%ច្បាប់",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header3" => array(
                "label" => "សរុប",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header4" => array(
                "label" => "ខែទី១",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header5" => array(
                "label" => "ខែទី២",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header6" => array(
                "label" => "ខែទី៣",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header7" => array(
                "label" => "សរុប",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header8" => array(
                "label" => "អនុ.ខែទី១",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header9" => array(
                "label" => "អនុ.ខែទី២",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header10" => array(
                "label" => "អនុ.ខែទី៣",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header11" => array(
                "label" => "សរុប",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header12" => array(
                "label" => "ខែទី៤",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header13" => array(
                "label" => "ខែទី៥",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header14" => array(
                "label" => "ខែទី៦",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header15" => array(
                "label" => "សរុប",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header16" => array(
                "label" => "អនុ.ខែទី៤",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header17" => array(
                "label" => "អនុ.ខែទី៥",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header18" => array(
                "label" => "អនុ.ខែទី៦",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header19" => array(
                "label" => "សរុប",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header20" => array(
                "label" => "ខែទី៧",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header21" => array(
                "label" => "ខែទី៨",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header22" => array(
                "label" => "ខែទី៩",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header23" => array(
                "label" => "សរុប",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header24" => array(
                "label" => "អនុ.ខែទី៧",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header25" => array(
                "label" => "អនុ.ខែទី៨",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header26" => array(
                "label" => "អនុ.ខែទី៩",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header27" => array(
                "label" => "សរុប",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header28" => array(
                "label" => "ខែទី១០",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header29" => array(
                "label" => "ខែទី១១",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header30" => array(
                "label" => "ខែទី១២",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header31" => array(
                "label" => "សរុប",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header32" => array(
                "label" => "អនុ.ខែទី១០",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header33" => array(
                "label" => "អនុ.ខែទី១១",
                "rowspan" => 0,
                "colspan" => 0,
            ),
            "header34" => array(
                "label" => "អនុ.ខែទី១២",
                "rowspan" => 0,
                "colspan" => 0,
            )
        );

        $dataFillables = array(
            "field1" => "id",
            "field2" => "entity",
            "field3" => "summary_budget",
            "field4" => array(
                "childField" => array(
                    "field1" => "account_group",
                    "field2" => "account",
                    "field3" => "sub_account",
                    "field4" => "department",
                    "field5" => "finance_rule",
                    "field6" => "amount",
                    "field7" => "percentage_implementing_rule",
                    "fiedl8" => "costing_plan_semester1",
                    "fiedl9" => "costing_plan_semester2",
                    "fiedl10" => "costing_plan_semester3",
                    "fiedl11" => "costing_plan_semester4",
                    "fiedl12" => "remark"
                )
            )
        );

        $data = array(
            "data_fields" => $this->dataFields(),
            "data" => $this->db_table::getRevPro($filter),
            "account_group_id" => AccountGroup::getAccGroupOpts($filter),
            "account_id" => [],
            "sub_account_id" => [],
            "entity_id" => $entities,
            // "entity_member_id" => $entity_members,
            "dataHeaders" => $dataHeaders,
            "dataSubHeaders" => $dataSubHeaders,
            "dataFillables" => $dataFillables,
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