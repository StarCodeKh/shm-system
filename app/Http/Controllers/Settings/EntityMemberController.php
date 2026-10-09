<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings\Entity;
use App\Models\Settings\EntityMember;
use App\Models\Settings\Status;
use Auth;
use CommonService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EntityMemberController extends Controller
{
    protected $db_table;

    public function __construct()
    {
        $this->middleware('auth');
        $this->db_table = new EntityMember;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function dataFields()
    {
        // Loop Data Field From Table to put in array for mapping data field to search data table
        $data['tables'] = DB::select('show columns from '.DB::getTablePrefix().($this->db_table)->getTable());
        $str = '';
        for ($i = 0; $i < count($data['tables']); $i++) {
            $str .= $data['tables'][$i]->Field.',';
        }
        $newArr = explode(',', $str);
        array_pop($newArr);
        $dataFields = $newArr;

        return $dataFields;
    }

    public function index(Request $request)
    {
        $input = $request->all();
        $dataFields = $this->dataFields();
        $filter = CommonService::getFilter($input);
        $entities = Entity::getSingleOpt();

        $data = [
            'data_fields' => $this->dataFields(),
            'data' => $this->db_table::getMemberByEntity($filter),
            'entity_id' => $entities,
            'limit' => $filter['limit'],
            'total' => $this->db_table::getCount($filter),
        ];

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
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
        $data = [
            'success' => $boolen,
            'message' => $message,
            'data' => $table,
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
        $table = $table = $this->db_table::find($id);
        $data = [
            'data' => $table,
        ];

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
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
        $data = [
            'success' => $boolen,
            'message' => $message,
            'data' => $this->db_table::findOrFail($id),
        ];

        return response()->json($data, $status);
    }

    public function dataForm($input)
    {
        $arr = $input;
        $push_array = array_merge([['created_by' => Auth::user()->id]]);
        $arraySingle = array_merge($arr, $push_array);
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
        $data = [
            'success' => $boolen,
            'message' => $message,
        ];

        return response()->json($data, $status);
    }
}
