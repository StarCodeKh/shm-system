<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\AccountType;
use App\Models\Settings\Status;
use App\Services\FormSchema;
use Auth;
use CommonService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountTypeController extends Controller
{
    protected $PERMISSION_ACTION_LIST;

    protected $PERMISSION_ACTION_CREATE;

    protected $PERMISSION_ACTION_SHOW;

    protected $PERMISSION_ACTION_UPDATE;

    protected $PERMISSION_ACTION_DELETE;

    protected $PERMISSION_ACTION_ACTIVITY_DETAIL;

    protected $db_table;

    public function __construct()
    {
        $this->db_table = new AccountType;
        $this->PERMISSION_ACTION_LIST = 'list_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_CREATE = 'create_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_SHOW = 'show_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_UPDATE = 'update_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_DELETE = 'delete_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_DELETE = 'activity_detail_'.$this->db_table->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $data = [];
        if (isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST] == true) {
            $input = $request->all();
            $filter = CommonService::getFilter($input);
            $data = [
                'data' => $this->db_table::getData($filter),
                'limit' => $filter['limit'],
                'total' => $this->db_table::getTotal($filter),
                'permissions' => CommonService::permission($this->db_table),
                'role_permissions' => CommonService::getPermissionsViaRoles(),
                'model_name' => $this->db_table->getTable(),
                'data_fields' => $this->db_table::dataFields(),
                'form_attributes' => FormSchema::fields($this->db_table->getTable()),
                'type_code' => AccountType::getAccTypeCode(),
            ];
            $status = 200;
            $data['is_display'] = true;
            $data['message'] = trans('common.msg_save_successfully');

            return response()->json($data, $status);
        } else {
            $status = 500;
            $data['is_display'] = false;
            $data['message'] = trans('common.msg_no_permission_access');

            return response()->json($data, $status);
        }
    }

    public function store(Request $request)
    {
        $data = [];
        if (isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_CREATE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_CREATE] == true) {
            $input = $request->all();
            $dataFields = $this->dataForm($input);
            $data['data'] = $this->db_table::create($dataFields);
            if ($data['data']) {
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_save_successfully');
            } else {
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        } else {
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = [
            'success' => $boolen,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($data, $status);
    }

    public function show($id)
    {
        $data = [];
        if (isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_SHOW] == true) {
            $query = $this->db_table::find($id);
            if ($query) {
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_successfully');
                $data['data'] = $query;
                $data['entity_id'] = Entity::getSingleOpt();
                $data['position'] = Position::getSingleOpt();
                $data['title'] = Title::getSingleOpt();
            } else {
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        } else {
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = [
            'success' => $boolen,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($data, $status);
    }

    public function update(Request $request, $id)
    {
        $data = [];
        if (isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_UPDATE] == true) {
            $input = $request->all();
            $dataFields = $this->dataForm($input);
            $table = $table = $this->db_table::find($id);
            $table->update($dataFields);
            if ($table) {
                $data = $this->db_table::findOrFail($id);
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_update_successfully');
            } else {
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }
        } else {
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = [
            'success' => $boolen,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($data, $status);
    }

    public function dataForm($input)
    {
        $arr = $input;
        $pushArray = array_merge([['created_by' => Auth::user()->id]]);
        $arraySingle = array_merge($arr, $pushArray);
        $result = call_user_func_array('array_merge', $arraySingle);
        $dataFields = $result;

        return $dataFields;
    }

    public function destroy($id)
    {
        if (isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_DELETE]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_DELETE] == true) {
            $table = $this->db_table::find($id);
            $table->update(['status' => Status::DELETED]);
            if ($table) {
                $status = 200;
                $boolen = true;
                $message = trans('common.msg_delete_successfully');
            } else {
                $status = 500;
                $boolen = false;
                $message = trans('common.error_msg');
            }

        } else {
            $status = 500;
            $boolen = false;
            $message = trans('common.msg_no_permission_access');
        }
        $data = [
            'success' => $boolen,
            'message' => $message,
        ];

        return response()->json($data, $status);
    }
}
