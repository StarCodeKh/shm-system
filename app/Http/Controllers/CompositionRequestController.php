<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompositionRequest;
use DB;
use Auth;
use CommonService;

class CompositionRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $db_table;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_title';
        $this->dbTable = new CompositionRequest;
        $this->langPath = $this->path;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        //
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
        $input["created_by"] = Auth::user()->id;
        $input["modified_by"] = Auth::user()->id;
        $dateCreate = date("Y-m-d", strtotime($input["request_date"]));
        $input["request_date"] = $dateCreate;
        $entityId = $input["entity"]["id"];
        $entityName = $input["entity"]["label"];
        $input["entity"] = $entityName;
        $input["entity_id"] = $entityId;
        $input["request_from"] = $input["request_from"]["label"];
        $table = CompositionRequest::create($input);

        for($i = 0; $i < sizeof($input["officerRequest"]); $i++){
            DB::table("incentive_composition_officer")->insert(
                [
                    "incentive_composition_request_id" => $table->id,
                    "from_officer_id" => $input["officerRequest"][$i]["from_officer"]["id"],
                    "to_officer_id" => $input["officerRequest"][$i]["to_officer"]["id"],
                ]
            );
        }

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
        $table = CompositionRequest::find($id);
        $incentiveCompositionOfficers = DB::table("incentive_composition_officer as ico")
                                        ->Select("ico.*","io.updated_bank_acc as from_acc_no" , "io2.updated_bank_acc as to_acc_no" , "io.first_name as from_first_name", "ico.from_officer_id as from_officer", "io.last_name as from_last_name", "io2.first_name as to_first_name", "io2.last_name as to_last_name", "ico.to_officer_id as to_officer")
                                        ->Join("incentive_officer as io", "io.id", "=", "ico.from_officer_id")
                                        ->Join("incentive_officer as io2", "io2.id", "=", "ico.to_officer_id")
                                        ->Where("ico.incentive_composition_request_id", $id)
                                        ->get();

        $compositionOfficers = array();
        foreach($incentiveCompositionOfficers as $compositionOfficerInfo){
            $infoFromOfficer = array(
                "id" => $compositionOfficerInfo->from_officer,
                "label" => $compositionOfficerInfo->from_first_name . ' ' .$compositionOfficerInfo->from_last_name,
                "from_acc_no" => $compositionOfficerInfo->from_acc_no,
            );
            $infoToOfficer = array(
                "id" => $compositionOfficerInfo->to_officer,
                "label" => $compositionOfficerInfo->to_first_name . ' ' .$compositionOfficerInfo->to_last_name,
                "to_acc_no" => $compositionOfficerInfo->to_acc_no,
            );
            $compositionOfficers[] = array(
                "from_officer" => $infoFromOfficer,
                "to_officer" => $infoToOfficer
            );
        }
        $data = array(
            "data" => $table,
            "incentiveComposition" => $compositionOfficers
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
        $input["modified_by"] = Auth::user()->id;
        $dateCreate = date("Y-m-d", strtotime($input["request_date"]));
        $input["request_date"] = $dateCreate;
        if(isset($input["entity"]["id"])){
            $entityId = $input["entity"]["id"];
            $entityName = $input["entity"]["label"];
            $input["entity"] = $entityName;
            $input["entity_id"] = $entityId;
        }else{
            $input["entity"] = $input["entity"];
        }
        if(isset($input["request_from"]["label"])){
            $input["request_from"] = $input["request_from"]["label"];
        }else{
            $input["request_from"] = $input["request_from"];
        }

        $table = CompositionRequest::where('id', $id)->update(
            [
                    'request_title' => $input["request_title"],
                    'reference' => $input["reference"],
                    'request_from' => $input["request_from"],
                    'description' => $input["description"],
                    'letter_type' => $input["letter_type"],
                    'entity' => $input["entity"],
                    'entity_id' => $input["entity_id"],
                    'attach_file' => $input["attach_file"],
                    'request_date' => $input["request_date"],
                    'effective_date' => $input["effective_date"],
                    'modified_by' => Auth::user()->id,
            ]
            );

        DB::table("incentive_composition_officer")->where("incentive_composition_request_id", $id)->update(["status" => 4]);
        for($i = 0; $i < sizeof($input["officerRequest"]); $i++){
            DB::table("incentive_composition_officer")->insert(
                [
                    "incentive_composition_request_id" => $id,
                    "from_officer_id" => $input["officerRequest"][$i]["from_officer"]["id"],
                    "to_officer_id" => $input["officerRequest"][$i]["to_officer"]["id"],
                ]
            );
        }

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
            "message" => $message
        );
        return response()->json($data, $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $table = CompositionRequest::where('id', $id)->update(["status" => 4]);
        DB::table("incentive_composition_officer")->where("incentive_composition_request_id", $id)->update(["status" => 4]);
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
