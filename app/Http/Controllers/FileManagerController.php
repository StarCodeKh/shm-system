<?php

namespace App\Http\Controllers;

use App\Models\FileManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Auth;
use CommonService;
use App\Models\Settings\Status;

class FileManagerController extends Controller
{
    // File types the file manager accepts (documents and images; no scripts or archives)
    const UPLOAD_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];

    protected $PERMISSION_ACTION_LIST;
    protected $PERMISSION_ACTION_CREATE;
    protected $PERMISSION_ACTION_SHOW;
    protected $PERMISSION_ACTION_UPDATE;
    protected $PERMISSION_ACTION_DELETE;
    protected $db_table;

    public function __construct()
    {
        $this->middleware('auth');
        $this->db_table = new FileManager;

        $this->PERMISSION_ACTION_LIST = "list_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_CREATE = "create_".$this->db_table->getTable();
        $this->PERMISSION_ACTION_SHOW = "show_".$this->db_table->getTable();
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
                "limit" => $filter["limit"],
                "total" => $this->db_table::getTotal($filter),
                "permissions" => CommonService::permission($this->db_table),
                "role_permissions" => CommonService::getPermissionsViaRoles(),
                "model_name" => $this->db_table->getTable(),
                "data_fields" => $this->db_table::dataFields(),
                "form_attributes" => $this->db_table::formAttributes(),
                "search_attributes" => $this->db_table::searchAttributes(),
                "type" => $this->db_table::typeOptions(),
            );
            $status = 200;
            $data["is_display"] = true;
            $data["message"] = trans('common.msg_save_successfully');
            return response()->json($data, $status);
        }else{
            $status = 500;
            $data["is_display"] = false;
            $data["message"] = trans('common.msg_no_permission_access');
            return response()->json($data, $status);
        }
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

    // Only the label and caption can change; the stored file, its path and type stay as uploaded
    public function update(Request $request, $id)
    {
        if (!CommonService::can($this->PERMISSION_ACTION_UPDATE)) {
            return response()->json(['success' => false, 'message' => trans('common.msg_no_permission_access')], 403);
        }
        $file = FileManager::query()->whereNotDeleted()->find($id);
        if (!$file) {
            return response()->json(['success' => false, 'message' => trans('common.error_msg')], 404);
        }
        $input = $request->all();
        $input = array_is_list($input) ? array_merge([], ...array_filter($input, 'is_array')) : $input;
        $fields = validator($input, [
            'file_name' => ['required', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:1000'],
        ])->validate();
        $file->update($fields + ['modified_by' => Auth::id()]);
        return response()->json(['success' => true, 'message' => trans('common.msg_update_successfully'), 'data' => $file->fresh()]);
    }

    public function dataForm($input){
        $arr = $input;
        $pushArray = array_merge(array(["created_by" => Auth::user()->id]));
        $arraySingle = array_merge($arr, $pushArray);
        $result = call_user_func_array('array_merge', $arraySingle);
        $dataFields = $result;
        return $dataFields;
    }

    // Hidden from the file manager (status 4, like other lists); the stored file stays, as records may still link to it
    public function destroy($id)
    {
        if (!CommonService::can($this->PERMISSION_ACTION_DELETE)) {
            return response()->json(['success' => false, 'message' => trans('common.msg_no_permission_access')], 403);
        }
        $file = FileManager::query()->whereNotDeleted()->find($id);
        if (!$file) {
            return response()->json(['success' => false, 'message' => trans('common.error_msg')], 404);
        }
        $file->update(['status' => Status::DELETED, 'modified_by' => Auth::id()]);
        return response()->json(['success' => true, 'message' => trans('common.msg_delete_successfully')]);
    }

    public function fileImageUploadService(Request $request){
        if (!CommonService::can('file_manager_upload')) {
            return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
        }
        // Only documents and images, checked by content as well as name, up to 20 MB
        $request->validate([
            'file' => 'required|file|max:20480|mimes:'.implode(',', self::UPLOAD_EXTENSIONS),
        ]);
        $file = $request->file('file');
        // The server picks the name: readable stem + random part, with the (validated) extension in lower case.
        // The original name is kept only as the label shown in the file manager.
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::UPLOAD_EXTENSIONS, true)) {
            $extension = $file->guessExtension();
        }
        $originalName = Str::limit(preg_replace('/[^\pL\pN\s._()-]+/u', '', $file->getClientOriginalName()), 180, '');
        $stem = Str::limit(Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)), 60, '');
        $size = $file->getSize();

        // Stored on the "public" disk (storage/app/public/uploads/YYYY/MM), served at /storage/... through the
        // public/storage link (php artisan storage:link): uploads stay apart from the app's own files.
        $stored = $file->storeAs('uploads/'.date('Y/m'), ($stem !== '' ? $stem.'-' : '').Str::random(10).'.'.$extension, 'public');
        $path = '/storage/'.$stored;

        $record = FileManager::create([
            "image" => self::thumbnail($extension, $path), // what the file manager shows
            "file_name" => $originalName ?: basename($stored),
            "path" => $path,                               // the file itself, for every type
            "type" => $extension,
            "extention" => $extension,
            "size" => $size,
            "created_by" => Auth::id(),
        ]);
        return response()->json(['success'=>true,'message'=>__('common.file_uploaded'),'data'=>['image_url'=>$path,'extension'=>$extension,'file'=>$record]], 200);
    }

    // Images show themselves; other files show their type icon
    private static function thumbnail(string $extension, string $path): string
    {
        return match ($extension) {
            'jpg', 'jpeg', 'png', 'gif', 'webp' => $path,
            'pdf' => '/images/file-icons/pdf.png',
            'doc', 'docx' => '/images/file-icons/doc.png',
            'xls', 'xlsx', 'csv' => '/images/file-icons/xls.png',
            default => '/images/attach.png',
        };
    }
}
