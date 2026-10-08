<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Settings\Currency;
use App\Models\Settings\Planning;
use Illuminate\Http\Request;
use Auth;
use CommonService;

class ConfigurationController extends Controller
{
    protected $db_table;
    protected $view_title;
    protected $lang_path;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path . '.entry_position_title';
        $this->db_table = new Configuration;
        $this->lang_path = $this->path;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $filter = CommonService::getFilter($input);
        $data = array(
            "data" => $this->db_table::getConfigs($filter),
            "config_language" => array(["label" => "En", "value" => "1"], ["label" => "Kh", "value" => "2"]),
            "config_currency" => CommonService::optVals(Currency::class, $filter, 1),
            "config_date_format" => array(["label" => "Y-m-d", "value" => "Y-m-d"], ["label" => "m-d-Y", "value" => "m-d-Y"]),
            "config_planning_year" => CommonService::optVals(Planning::class, $filter, 1),
        );
        return response()->json($data);
    }

    /**
     * The current planning year (config_planning_year), e.g. { id: 4, year: 2023, name_kh, name_en }.
     */
    public function planning()
    {
        $planning = Planning::find(setting('config_planning_year'));
        return response()->json([
            'success' => (bool) $planning,
            'data' => $planning ? [
                'id' => $planning->id,
                'year' => (int) $planning->year,
                'name_kh' => $planning->name_kh,
                'name_en' => $planning->name_en,
            ] : null,
        ]);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $conToSingleArr = call_user_func_array('array_merge', $input);
        $code = "config";
        foreach ($conToSingleArr as $key => $value) {
            $table = $this->db_table::where('key', $key)->first();
            if (substr($key, 0, strlen($code)) == $code) {
                if (!is_array($value)) {
                    $table->update(
                        [
                            "code" => $code,
                            "key" => $key,
                            "value" => $value,
                        ]
                    );
                } else {
                    $table->update(
                        [
                            "code" => $code,
                            "key" => $key,
                            "value" => $value,
                        ]
                    );
                }
            }
        }
        $status = 200;
        $boolen = true;
        $message = trans('common.msg_update_successfully');
        $data = array(
            "success" => $boolen,
            "message" => $message
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
     * Display the specified resource.
     */
    public function show($id)
    {
        $table = Configuration::find($id);
        $data = array(
            "data" => $table
        );
        return response()->json($data);
    }
}
