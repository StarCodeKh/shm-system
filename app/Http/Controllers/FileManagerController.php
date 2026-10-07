<?php

namespace App\Http\Controllers;

use App\Models\FileManager;

// PB
// BM = Budget Monitoring

use App\Imports\OfficerImport;
use App\Imports\IncomeImport;

use Maatwebsite\Excel\Facades\Excel;
use File;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use CommonService;

class FileManagerController extends Controller
{
    // File types the file manager accepts (documents and images; no scripts or archives)
    const UPLOAD_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];

    // Spreadsheet imports: the caller needs $permission and the file must be a real Excel / CSV file (max 10 MB).
    // Returns a response to send back when refused, otherwise null.
    protected function guardSpreadsheetImport(Request $request, string $permission)
    {
        if (!CommonService::can($permission)) {
            return response()->json(['success' => false, 'message' => trans('common.msg_no_permission_access')], 403);
        }
        $request->validate(['file' => 'required|file|max:10240|mimes:xlsx,xls,csv']);
        return null;
    }

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
        if ($denied = $this->guardSpreadsheetImport($request, 'import_officer')) return $denied;
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new OfficerImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>__('common.file_uploaded')], 200);
        }else{
            return response()->json(['success'=>false,'message'=>__('common.file_upload_failed')], 500);
        }
    }

    //

    public function fileIncomeUploadService(Request $request){
        if ($denied = $this->guardSpreadsheetImport($request, 'create_income')) return $denied;
        $path = $request->file('file')->getRealPath();
        $rows = Excel::import(new IncomeImport, $path);
        if($rows){
            return response()->json(['success'=>true,'message'=>__('common.file_uploaded')], 200);
        }else{
            return response()->json(['success'=>false,'message'=>__('common.file_upload_failed')], 500);
        }
    }

    // PB

    public function fileImageUploadService(Request $request){
        if (!CommonService::can('file_manager_upload')) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        // Only documents and images, checked by content as well as name, up to 20 MB
        $request->validate([
            'file' => 'required|file|max:20480|mimes:'.implode(',', self::UPLOAD_EXTENSIONS),
        ]);
        $file = $request->file('file');
        $date_create = date('d-M-Y/');
        $destinationPath = 'images/uploads/'.$date_create; // upload path
        // The server picks the name: random, with the (validated) extension in lower case. The original
        // name is kept only as the label shown in the file manager.
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::UPLOAD_EXTENSIONS, true)) {
            $extension = $file->guessExtension();
        }
        $originalName = Str::limit(preg_replace('/[^\pL\pN\s._()-]+/u', '', $file->getClientOriginalName()), 180, '');
        $stem = Str::limit(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)), 60, '');
        $fileName = ($stem !== '' ? $stem.'-' : '').Str::random(10).'.'.$extension;
        $file->move($destinationPath, $fileName); // uploading file to given path
        switch ($extension) {
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
            "file_name" => $originalName ?: $fileName,
            "path" => $imageUrl,
            "type" => $extension,
            "extention" => $extension
        ]);
        $imageUrl = '/'.$destinationPath.$fileName;
        return response()->json(['success'=>true,'message'=>__('common.file_uploaded'),'data'=>['image_url'=>$imageUrl,'extension'=>$extension]], 200);
    }
}
