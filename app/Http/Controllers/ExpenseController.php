<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Settings\Account;
use App\Models\Settings\Currency;
use App\Models\Settings\Entity;
use App\Models\Settings\PaymentMethod;
use App\Models\Settings\Status;
use Auth;
use CommonService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ExpenseController extends BaseController
{
    protected $PERMISSION_ACTION_LIST;

    protected $PERMISSION_ACTION_CREATE;

    protected $PERMISSION_ACTION_SHOW;

    protected $PERMISSION_ACTION_UPDATE;

    protected $PERMISSION_ACTION_DELETE;

    protected $PERMISSION_ACTION_DOWNLOAD;

    protected $PERMISSION_ACTION_IMPORT;

    protected $PERMISSION_ACTION_FILEMANAGER;

    protected $db_table;

    public function __construct()
    {
        $this->middleware('auth');
        $this->db_table = new Expense;
        $this->PERMISSION_ACTION_LIST = 'list_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_CREATE = 'create_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_SHOW = 'show_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_UPDATE = 'update_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_DELETE = 'delete_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_DOWNLOAD = 'download_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_IMPORT = 'import_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_FILEMANAGER = 'filemanager_'.$this->db_table->getTable();
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
                'form_attributes' => $this->db_table::formAttributes(),
                'currency_id' => Currency::getSingleOpt(),
                'entity_id' => Entity::getSingleOpt(),
                'payment_method_id' => PaymentMethod::getSingleOpt(),
                'account_code' => Account::getAccountCodeList(),
                'status' => Status::getSingleOpt('STATUS'),
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

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
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

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
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

    public function insertExpense(Request $request)
    {
        try {
            // ✅ Step 1: Validate inputs
            $validated = $request->validate([
                'expense_title' => 'required|string|max:120',
                'account' => 'required|integer',
                'payment_method_id' => 'required|integer',
                'category' => 'nullable|string|max:120',
                'amount' => 'required|numeric',
                'reference' => 'nullable|string|max:120',
                'description' => 'nullable|string',
                'entity_id' => 'required|integer',
                'exchange_rate' => 'nullable|numeric',
                'currency_id' => 'required|integer',
                'status' => 'required|integer',
            ]);
            // The signed-in user, never a value from the request
            $validated['created_by'] = Auth::id();
            // ✅ Step 2: Insert into DB
            $inserted = Expense::insertExpense($validated);
            if ($inserted) {
                return $this->successResponse(
                    ['expense' => $validated],
                    __('common.msg_save_successfully')
                );
            }

            return $this->errorResponse(__('common.error_msg'));
        } catch (ValidationException $e) {
            // ❌ Return validation errors
            return $this->errorResponse(__('common.validation_failed'), 422, $e->errors());
        } catch (\Exception $e) {
            report($e); // details go to the log, not to the client

            return $this->errorResponse(__('common.error_msg'), 500);
        }
    }
}
