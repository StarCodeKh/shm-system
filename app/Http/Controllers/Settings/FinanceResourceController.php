<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\FinanceResource;
use Illuminate\Http\Request;
use Auth;
use DB;
use CommonService;

class FinanceResourceController extends Controller
{
    protected $db_table;
    protected $view_title;
    protected $lang_path;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path . '.entry_account_title';
        $this->db_table = new FinanceResource;
        $this->lang_path = $this->path;
    }
    /**
     * Display a listing of the resource.
     */
    public function dataFields()
    {
        $data["tables"] = DB::select('show columns from ' . env("DB_PREFIX") . ($this->db_table)->getTable());
        $str = "";
        for ($i = 0; $i < count($data["tables"]); $i++) {
            $str .= $data["tables"][$i]->Field . ",";
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
        $data = array(
            "data_fields" => $this->dataFields(),
            "data" => $this->db_table::getFinanceResources($filter),
            "limit" => config_limit,
            "total" => $this->db_table::getCount($filter)
        );
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $dataFields = $this->dataForm($input);
        $table = $this->db_table::create($dataFields);
        if ($table) {
            $status = 200;
            $boolen = true;
            $message = trans('common.msg_save_successfully');
        } else {
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
     */
    public function show($id)
    {
        $table = $table = $this->db_table::find($id);
        $data = array(
            "data" => $table
        );
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $dataFields = $this->dataForm($input);
        $table = $table = $this->db_table::find($id);
        $table->update($dataFields);
        if ($table) {
            $status = 200;
            $boolen = true;
            $message = trans('common.msg_update_successfully');
        } else {
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

    public function dataForm($input)
    {
        $arr = $input;
        $push_array = array_merge(array(["created_by" => Auth::user()->id]));
        $arraySingle = array_merge($arr, $push_array);
        $result = call_user_func_array('array_merge', $arraySingle);
        $dataFields = $result;
        return $dataFields;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $table = $this->db_table::find($id);
        $table->update(["status" => 4]);
        if ($table) {
            $status = 200;
            $boolen = true;
            $message = trans('common.msg_delete_successfully');
        } else {
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
