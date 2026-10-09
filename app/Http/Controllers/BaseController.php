<?php

namespace App\Http\Controllers;

class BaseController extends Controller
{
    /**
     * ✅ Standard API success response
     * - If $data is empty, return null
     */
    protected function successResponse($data = [], $message = 'Success', $code = '000')
    {
        // If $data is empty, return null instead of []
        $responseData = (empty($data) || $data === []) ? null : $data;

        return response()->json([
            'errorCode' => $code,
            'errorMessage' => $message,
            'data' => $responseData,
        ]);
    }

    /**
     * ❌ Standard API error response
     * - If $data is empty, return null
     */
    protected function errorResponse($message = 'Fail', $code = '002', $data = [])
    {
        $responseData = (empty($data) || $data === []) ? null : $data;

        return response()->json([
            'errorCode' => $code,
            'errorMessage' => $message,
            'data' => $responseData,
        ]);
    }
}
