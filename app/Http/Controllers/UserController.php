<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use App\Models\Settings\Department;
use App\Models\Settings\Entity;
use App\Models\Settings\Position;
use App\Models\Settings\RoleUser;
use App\Models\Settings\Status;
use App\Models\Settings\Title;
use App\Models\UserDevice;
use App\User;
use Auth;
use CommonService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $PERMISSION_ACTION_LIST;

    protected $PERMISSION_ACTION_CREATE;

    protected $PERMISSION_ACTION_SHOW;

    protected $PERMISSION_ACTION_UPDATE;

    protected $PERMISSION_ACTION_DELETE;

    protected $db_table;

    public function __construct()
    {
        $this->middleware('auth');
        $this->db_table = new User;

        $this->PERMISSION_ACTION_LIST = 'list_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_CREATE = 'create_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_SHOW = 'show_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_UPDATE = 'update_'.$this->db_table->getTable();
        $this->PERMISSION_ACTION_DELETE = 'delete_'.$this->db_table->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        if (isset(CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST]) && CommonService::permission($this->db_table->getTable())[$this->PERMISSION_ACTION_LIST] == true) {
            if (isset($input['officer_id'])) {
                $input['officer_id'] = $input['officer_id'];
            }
            $filter = CommonService::getFilter($input);
            $data = [
                'data' => $this->db_table::getData($filter),
                'permissions' => CommonService::permission($this->db_table),
                'role_permissions' => CommonService::getPermissionsViaRoles(),
                'model_name' => $this->db_table->getTable(),
                // The login tab of an officer profile (it always sends officer_id) picks that officer
                'officer_id' => isset($filter['data']['officer_id']) ? Officer::getSingleOptById($filter['data']['officer_id']) : [],
                'role' => RoleUser::getSingleValueOpt(),
                'gender' => [
                    ['label' => 'ប្រុស', 'value' => 'ប្រុស'],
                    ['label' => 'ស្រី', 'value' => 'ស្រី'],
                ],
                'status' => Status::getSingleOpt('STATUS'),
                'form_attributes' => User::formAttributes(),
                'data_fields' => User::dataFields(),
                'limit' => $filter['limit'],
                'total' => User::getTotal($filter),
            ];
            $status = 200;
            $data['is_display'] = true;
            $data['message'] = trans('common.msg_successfully');

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
            $rules = [
                'username' => 'required|string|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed|min:6|string|regex:/[A-Z]/|regex:/[@$!%*#?&]/',
            ];
            $messages = [
                'password.confirmed' => 'Password not match!',
                'password.regex' => 'Password at least one uppercase[A-Z] and on special character [@$!%*#?&]!',
            ];
            $validator = Validator::make($dataFields, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors(),
                ], 200);
            }

            unset($dataFields['password_confirmation']);
            $dataFields['password'] = bcrypt($dataFields['password']);
            $query = $this->db_table::create($dataFields);
            if ($query) {
                $arraySingle = call_user_func_array('array_merge', $input);
                $user = User::find($query->id);
                // Permissions come from the role; no direct grants
                if (! empty($arraySingle['role'])) {
                    $user->syncRoles([$arraySingle['role']]);
                }
                $data['data'] = $query;
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
                $data['role'] = RoleUser::getSingleValueOpt();
                $data['departments'] = Department::getSingleOpt();
                $data['entities'] = Entity::getSingleOpt();
                $data['position'] = Position::getSingleOpt();
                $data['title'] = Title::getSingleOpt();
                $data['status'] = Title::getSingleOpt('STATUS');
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
            $arraySingle = call_user_func_array('array_merge', $input);
            $dataFields = $this->dataForm($input);
            $user = User::where('id', $id)->first();

            if ($user->username != $dataFields['username']) {
                $validator = Validator::make($dataFields, [
                    'username' => 'required|string|max:255|unique:users,username',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors(),
                    ], 200);
                }
            }
            if ($user->email != $dataFields['email']) {
                $validator = Validator::make($dataFields, [
                    'email' => 'required|email|unique:users,email,',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors(),
                    ], 200);
                }
            }

            if (isset($dataFields['password'])) {
                // 'regex:/[a-z]/',            // at least one lowercase
                // 'regex:/[A-Z]/',            // at least one uppercase
                // 'regex:/[0-9]/',            // at least one digit
                // 'regex:/[@$!%*#?&]/',       // at least one special char
                $rules = ['password' => 'confirmed|min:6|string|regex:/[A-Z]/|regex:/[@$!%*#?&]/'];
                $messages = [
                    'password.confirmed' => 'Password not match!',
                    'password.regex' => 'Password at least one uppercase[A-Z] and on special character [@$!%*#?&]!',
                ];
                $validator = Validator::make($dataFields, $rules, $messages);
                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => $validator->errors(),
                    ], 200);
                }

                $dataFields['password'] = bcrypt($dataFields['password']);
            } else {
                unset($dataFields['password']);
            }
            unset($dataFields['password_confirmation']);
            $table = $this->db_table::find($id);
            $table->update($dataFields);
            if ($table) {
                // One role per user: replace it (permissions come from the role, never granted one by one)
                $user = User::findOrFail($id);
                if (! empty($arraySingle['role'])) {
                    $user->syncRoles([$arraySingle['role']]);
                }
                // Reponse Result
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
        $dataFields = call_user_func_array('array_merge', $arraySingle);
        // The role is saved with syncRoles(), not in the users table
        unset($dataFields['role'], $dataFields['role_id']);

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

    public function getProfile(Request $request)
    {

        $input = $request->all();
        $data = [];
        $officer = '';
        if (isset($input['officer_id']) && $input['officer_id'] != null) {
            $officer = Officer::where('id', $input['officer_id'])->first();
            if ($officer) {
                $id = $officer->user_id;
            } else {
                $id = null;
            }
        } elseif (! empty($input['user_id']) && (int) $input['user_id'] !== (int) Auth::user()->id) {
            // Someone else's profile: needs the users "show" permission
            if (! CommonService::can('show_users')) {
                return response()->json(['message' => trans('common.msg_no_permission_access')], 403);
            }
            $id = (int) $input['user_id'];
        } else {
            $id = Auth::user()->id;
        }
        $data['data'] = $this->db_table::getProfile($id);
        $data['form_attributes'] = $this->db_table::formAttributes();
        $data['role'] = RoleUser::getSingleValueOpt();
        $data['departments'] = Department::getSingleOpt();
        $data['entities'] = Entity::getSingleOpt();
        $data['position'] = Position::getSingleOpt();
        $data['title'] = Title::getSingleOpt();
        $data['status'] = Title::getSingleOpt('STATUS');
        $data = [
            'data' => $data,
        ];

        return response()->json($data);
    }

    public function getUserDevice(Request $request)
    {
        $input = $request->all();
        if (isset($input['officer_id']) && $input['officer_id'] != null) {
            $filter['officer_id'] = $input['officer_id'];
        }
        $filter = CommonService::getFilter($input);
        $table = UserDevice::getData($filter);
        $data = [
            'data' => $table,
            'limit' => $filter['limit'],
            'total' => UserDevice::getTotal($filter),
            'permissions' => CommonService::permission($this->db_table),
            'role_permissions' => CommonService::getPermissionsViaRoles(),
            'model_name' => $this->db_table->getTable(),
            'data_fields' => UserDevice::dataFields(),
            'form_attributes' => $this->db_table::formAttributes(),
        ];

        return response()->json($data);
    }

    public function updateProfile(Request $request)
    {
        $id = Auth::user()->id;
        $data = $request->all();
        $table = $this->db_table::where('id', $id)->update([
            'fullname' => $data['fullname'],
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'email' => $data['email'],
            'avatar' => $data['avatar'],
        ]);
        $data = [
            'data' => $data,
        ];

        return response()->json($data);
    }
}
