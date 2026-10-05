<?php

namespace App\Http\Controllers;

use App\Models\FileManager;

use App\Imports\ObjectiveImport;
use App\Imports\ProgramImport;
use App\Imports\SubProgramImport;
use App\Imports\ClusterActivityImport;
use App\Imports\ActivityImport;

use App\Imports\AccountGroupImport;
use App\Imports\AccountImport;

use App\Imports\CeilingEntityImport;
use App\Imports\CeilingDataEntityImport;

use App\Imports\PIPInvestmentImport;
use App\Imports\PIPInvDetailImport;

use App\Imports\BSPAssignProgramImport;

// PB
use App\Imports\PBCostingImport;
use App\Imports\PBRevenueImport;
use App\Imports\PBExpenditureImport;
// BM = Budget Monitoring
use App\Imports\BMRevProgramImport;
use App\Imports\BMExpProgramImport;

use App\Imports\ItemImport;
use App\Imports\UnitImport;


use App\Imports\CsvImport;
use App\Imports\OfficerImport;
use App\Imports\OfficerFileManagerIncentiveImport;
use App\Imports\IncomeImport;
use App\Imports\OutcomeImport;
use App\Imports\EntityImport;
use App\Imports\EntityMemberImport;

use App\Imports\UserImport;
// use Excel;
use Maatwebsite\Excel\Facades\Excel;
use File;

use Illuminate\Http\Request;
use Auth;
use CommonService;

class FileManagerController extends Controller
{
    protected $PERMISSION_ACTION_LIST;
    protected $PERMISSION_ACTION_CREATE;
    protected $PERMISSION_ACTION_SHOW;
    protected $PERMISSION_ACTION_EDIT;
    protected $PERMISSION_ACTION_UPDATE;
    protected $PERMISSION_ACTION_DELETE;
    protected $db_table;
    public $path = "admin/setting";

    public function __construct()
    {
        $this->middleware('auth');
        $this->view_title = $this->path.'.entry_role';
        $this->db_table = new FileManager;
        $this->lang_path = $this->path;

        $this->PERMISSION_ACTION_LIST = "list_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_CREATE = "create_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_SHOW = "show_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_EDIT = "edit_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_UPDATE = "update_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_DELETE = "delete_".$this->db_table->getTable();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST] == true){
            $input = $request->all();
            $filter = CommonService::getFilter($input);
            $data = array(
                "data" => $this->db_table::getData($filter),
                "limit" => config_limit,
                "total" => $this->db_table::getTotal($filter),
                "permissions" => CommonService::permission($this->db_table),
                "role_permissions" => CommonService::getPermissionsViaRoles(),
                "model_name" => $this->db_table->getTable(),
                "data_fields" => $this->db_table::dataFields(),
                "form_attributes" => $this->db_table::formAttributes(),
            );
            $status = 200;
            $boolen = true;
            $data["message"] = trans('common.msg_save_successfully');
            return response()->json($data, $status);
        }else{
            $status = 500;
            $boolen = false;
            $data["message"] = trans('common.msg_no_permission_access');
            return response()->json($data, $status);
        }
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
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_CREATE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_CREATE] == true){
            $input = $request->all();
            $dataFields = $this->dataForm($input);
            $data["data"] = $this->db_table::create($dataFields);
            if($data["data"]){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_save_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $data,
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
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW] == true){
            $query = $this->db_table::find($id);
            if($query){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_successfully');
                $data["data"] = $query;
                $data["permission_lists"] = CommonService::getAllPermissionData();
                $data["permission_by_roles"] = CommonService::getPermissionsViaRolesByUser($id);
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $data,
        );
        return response()->json($data, $status);
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
        $data = array();
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE] == true){
            $input = $request->all();
            $dataFields = $this->dataForm($input);
            $table = $table=$this->db_table::find($id);
            $table->update($dataFields);
            if($table){
                $data = $this->db_table::findOrFail($id);
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_update_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message,
            "data" => $data
        );
        return response()->json($data, $status);
    }

    public function dataForm($input){
        $arr = $input;
        $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
        $arraySingle = array_merge($arr, $pushArray);
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
        if(isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_DELETE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_DELETE] == true){
            $table=$this->db_table::find($id);
            $table->update(["status" => 4]);
            if($table){
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_delete_successfully');
            }else{
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
            
        }else{
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = array(
            "success" => $boolen,
            "message" => $message
        );
        return response()->json($data, $status);
    }

    public function fileUploadService(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new OfficerImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
        // $dd = Excel::selectSheets('sheet1')->load();
        // $array = (new CsvImport)->import($path);
        // $result = Excel::import($path,function($reader){
        //     $reader->all();
        // })->get();
        // print_r($rows);
        // $input = $request->all();
        // // $file = $request->file('file');
        // $file = $input['file'];
        // $date_create = date('d-M-Y/');
        // $destinationPath = 'images/uploads/'.$date_create; // upload path
        // $extension = $request->file('file')->getClientOriginalExtension(); // getting image extension
        // // $fileName = $this->generateRandomString(10).'.'.$extension; // renameing image
        // // $fileName = rand(11111111,99999999).'.'.$extension; // renameing image
        // $fileName = $file->getClientOriginalName();
        // $request->file('file')->move($destinationPath, $fileName); 
        // $fileUrl = '/'.$destinationPath.$fileName;
        // $path = $request->file('file')->getRealPath();
        // $result = Excel::import($file,function($reader){
        //     $reader->all();
        // })->get();
        // if($request->hasFile('file')){
        //     // $path = $request->file('file')->getRealPath();
        //     // $reader = Excel::import('./images/uploads/28-Jun-2020/Book1.xlsx');
        //     // $data = Excel::import($path, null, \Maatwebsite\Excel\Excel::XLSX);
        //     // print_r($datas);
        //     // $file = $request->file('file');
        //     // $fileName = $file->getClientOriginalName();
        //     $path = "./images/uploads/28-Jun-2020/Book1.xlsx";
		// 	$data = Excel::import($path, function($reader) {})->get();
		// 	if(!empty($data) && $data->count()){
		// 		foreach ($data->toArray() as $key => $value) {
		// 			if(!empty($value)){
		// 				foreach ($value as $v) {		
		// 					$insert[] = ['title' => $v['title'], 'description' => $v['description']];
		// 				}
		// 			}
		// 		}
		// 		prin_r($insert);
		// 		if(!empty($insert)){
		// 			// Item::insert($insert);
		// 			return back()->with('success','Insert Record successfully.');
		// 		}
		// 	}
		// }
		// return back()->with('error','Please Check your file, Something is wrong there.');
        // $input = $request->all();
        // $path = $request->file('file');//$request->file('import_file')->getRealPath();
        // $data = Excel::load($path, function($reader) {})->get();
        // print_r($data);
        // return false;
        // $attachments_files = $request->file('file');
        // $file = $input['file'];
        // $date_create = date('d-M-Y/');
        // $destinationPath = 'images/uploads/'.$date_create; // upload path
        // $extension = $request->file('file')->getClientOriginalExtension(); // getting image extension
        // // $fileName = $this->generateRandomString(10).'.'.$extension; // renameing image
        // // $fileName = rand(11111111,99999999).'.'.$extension; // renameing image
        // $fileName = $file->getClientOriginalName();
        // $request->file('file')->move($destinationPath, $fileName); 
        // $fileUrl = '/'.$destinationPath.$fileName;
        // $createImage = FileManager::create([
        //     "file_name" => $fileName,
        //     "url" => $fileUrl,
        //     "file_type" => $file->getClientOriginalExtension(),
        //     "extention" => $file->getClientOriginalExtension()
        // ]);
        // $fileUrl = '/'.$destinationPath.$fileName;
        // return response()->json(['success'=>true,'message'=>'File uploaded successfully.','data'=>['file_url'=>$fileUrl,'extension'=>$extension]], 200);
    }
    
    public function fileUploadObjective(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new ObjectiveImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }
    public function fileUploadProgram(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new ProgramImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }
    public function fileUploadSubProgram(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new SubProgramImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }
    public function fileUploadClusterActivity(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new ClusterActivityImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadActivity(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new ActivityImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadAccountGroup(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new AccountGroupImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }
    public function fileUploadAccount(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new AccountImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }
    public function fileUploadSubAccount(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new AccountImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadEntity(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new EntityImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadEntityMember(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new EntityMemberImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadCeilingEntity(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new CeilingEntityImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadCeilingDataEntity(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new CeilingDataEntityImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadPIPInvestment(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new PIPInvestmentImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadPIPInvDetail(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new PIPInvDetailImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadBSPAssignProgram(Request $request){
        // dd($request->all());
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new BSPAssignProgramImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    // 
    public function fileUploadOfficer(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new OfficerImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function OfficerIncentiveFileManagerImport(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new OfficerFileManagerIncentiveImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileIncomeUploadService(Request $request){
        $data = $request->all();
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new IncomeImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileOutcomeUploadService(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new OutcomeImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }
    
    public function fileUploadItem(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new ItemImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadUnit(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new UnitImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    // PB
    public function fileUploadPBCosting(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new PBCostingImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadPBRevenue(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new PBRevenueImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadPBExpenditure(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new PBExpenditureImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadBMRevProgramImport(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new BMRevProgramImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileUploadBMExpProgramImport(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new BMExpProgramImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function fileImageUploadService(Request $request){
        $input = $request->all();
        $attachments_files = $request->file('file');
        $file = $input['file'];
        $date_create = date('d-M-Y/');
        $destinationPath = 'images/uploads/'.$date_create; // upload path
        $extension = $request->file('file')->getClientOriginalExtension(); // getting image extension
        // $fileName = $this->generateRandomString(10).'.'.$extension; // renameing image
        // $fileName = rand(11111111,99999999).'.'.$extension; // renameing image
        $fileName = $file->getClientOriginalName();
        $request->file('file')->move($destinationPath, $fileName); // uploading file to given path
        // $imageUrl = config_url.'/'.$destinationPath.$fileName;
        switch ($file->getClientOriginalExtension()) {
            case 'pdf':
                $imageUrl = '/images/attach.png';
                break;
            case 'doc':
                $imageUrl = '/images/attach.png';
                break;
            case 'docx':
                $imageUrl = '/images/attach.png';
                break;
            case 'xlsx':
                $imageUrl = '/images/attach.png';
                break;
            default:
                $imageUrl = '/'.$destinationPath.$fileName;
                break;
        }
        $createImage = FileManager::create([
            "image" => $imageUrl,
            "file_name" => $fileName,
            "path" => $imageUrl,
            "type" => $file->getClientOriginalExtension(),
            "extention" => $file->getClientOriginalExtension()
        ]);
        $imageUrl = '/'.$destinationPath.$fileName;
        return response()->json(['success'=>true,'message'=>'File uploaded successfully.','data'=>['image_url'=>$imageUrl,'extension'=>$extension]], 200);
    }

    public function fileUploadUser(Request $request){
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new UserImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>'File uploaded successfully.'], 200);
        }else{
            return response()->json(['success'=>false,'message'=>'File upload is failed!'], 500);
        }
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}