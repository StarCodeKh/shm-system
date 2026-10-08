<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\FormSchema;
use App\Models\Settings\Title;
use Illuminate\Http\Request;
use Auth;
use CommonService;

class TitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $dbTable;
    protected $view_title;
    protected $langPath;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path . '.entry_title';
        $this->dbTable = new Title;
        $this->langPath = $this->path;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $filter = CommonService::getFilter($input);
        $data = array(
            "data" => $this->dbTable::getData($filter),
            "limit" => config_limit,
            "total" => $this->dbTable::getTotal($filter),
            "data_fields" => $this->dbTable::dataFields(),
            "form_attributes" => FormSchema::fields($this->dbTable->getTable()),
            "permissions" => CommonService::permission($this->dbTable),
            "model_name" => $this->dbTable->getTable()
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
        $table = $this->dbTable::create($dataFields);
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
        $table = $table = $this->dbTable::find($id);
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
        $table = $table = $this->dbTable::find($id);
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
            "data" => $this->dbTable::findOrFail($id)
        );
        return response()->json($data, $status);
    }

    public function dataForm($input)
    {
        $arr = $input;
        $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
        $arraySingle = array_merge($arr, $pushArray);
        $result = call_user_func_array('array_merge', $arraySingle);
        $dataFields = $result;
        return $dataFields;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $table = $this->dbTable::find($id);
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
