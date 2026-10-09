<?php

namespace App\Http\Controllers;

use App\Models\Settings\Status;
use App\Models\TaxableSalary;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaxableSalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $filter = [
            'offset' => isset($input['offset']) ? $input['offset'] : setting('config_offset'),
            'limit' => isset($input['limit']) ? $input['limit'] : setting('config_limit'),
            'sort' => isset($input['sort']) ? $input['sort'] : setting('config_sort'),
            'order' => isset($input['order']) ? $input['order'] : setting('config_order'),
        ];
        $table = TaxableSalary::orderByFilter($filter)
            ->offset($filter['offset'])
            ->limit($filter['limit'])
            ->get();
        $data = [
            'data' => $table,
            'total' => count($table),
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
        $input['created_by'] = Auth::user()->id;
        $input['modified_by'] = Auth::user()->id;
        $table = TaxableSalary::create($input);
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
        $table = TaxableSalary::find($id);
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
        $input['modified_by'] = Auth::user()->id;
        $table = TaxableSalary::where('id', $id)->update($input);
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
            'data' => TaxableSalary::findOrFail($id),
        ];

        return response()->json($data, $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $table = TaxableSalary::where('id', $id)->update(['status' => Status::DELETED]);
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
