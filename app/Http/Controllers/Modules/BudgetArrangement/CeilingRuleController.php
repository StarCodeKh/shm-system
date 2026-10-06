<?php

namespace App\Http\Controllers\Modules\BudgetArrangement;

use App\Http\Controllers\Controller;
use App\Models\Modules\BudgetArrangement\CeilingRule;
use Illuminate\Http\Request;
use Auth;
use DB;

class CeilingRuleController extends Controller
{
    protected $db_table;
    public $path = "admin/modules/ceiling_rule";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->db_table = new CeilingRule;
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
        $filter = array(
            // "offset" => isset($input["offset"]) ? $input["offset"] : config_offset,
            "limit" => isset($input["limit"]) ? $input["limit"] : config_limit,
            "sort" => isset($input["sort"]) ? $input["sort"] : config_sort,
            "order" => isset($input["order"]) ? $input["order"] : config_order
        );
        $query = $this->db_table::orderByFilter($filter);
        $whereClause = $query;
        $whereClause->offset(($input["page_number"] - 1) * config_limit);       
        $whereClause->limit($filter["limit"]);
        if(!empty($input["search_field"])){
            // Any of the search fields may match, but the other filters still apply
            $whereClause->where(function ($q) use ($input, $dataFields) {
                foreach ($input["search_field"] as $search) {
                    $field = array_key_first($search);
                    if (in_array($field, $dataFields)) {
                        $q->orWhere($field, "Like","%".$search[$field]."%");
                    }
                }
            });
        }
        
        $table = collect($whereClause->get());
        // $data = array(
        //     "data_fields" => $this->dataFields(),
        //     "data" => $table,
        //     "limit" => config_limit,
        //     "total" =>collect($whereClause->count())
        // );
        $status = array(
            [
                "label" => "មិនទាន់ដំណើរការ",
                "value" => "មិនទាន់ដំណើរការ",
            ],
            [
                "label" => "បានបញ្ចប់",
                "value" => "បានបញ្ចប់",
            ],
            [
                "label" => "បានអនុម័ត",
                "value" => "បានអនុម័ត",
            ]
        );
        
        
        $data = array(
            "data_fields" => $this->dataFields(),
            "data" => $table,
            "status" => $status,
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