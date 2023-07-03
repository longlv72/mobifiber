<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CoreController extends Controller
{
    protected const IS_NOT_ROLL_BACK_DB = false;
    protected const IS_ROLLBACK_DB = true;
    protected const IS_COMMIT_DB = true;
    protected const IS_NOT_COMMIT_DB = false;
    public function __construct()
    {
        parent::__construct();
    }

    function responseJSONSuccess($is_commit_db = false, $message = "Thành công", $data = [], $_HTTP_CODE = JsonResponse::HTTP_OK) {
        if($is_commit_db) {
            DB::commit();
        }
        if($data) {
            return response()->json([
                'success'   => true,
                'message'   => $message,
                $data
            ], $_HTTP_CODE);
        }
        return response()->json([
            'success'   => true,
            'message'   => $message,
        ], $_HTTP_CODE);
    }
    
    function responseJSONSuccessWithData($is_commit_db = false, $message = "Thành công", $data = [], $_HTTP_CODE = JsonResponse::HTTP_OK) {
        if($is_commit_db) {
            DB::commit();
        }
        if($data) {
            return response()->json(
                    array_merge(
                    [
                        'success'   => true,
                        'message'   => $message,
                    ], 
                    $data
                ), 
                $_HTTP_CODE
            );
        }
        return response()->json([
            'success'   => true,
            'message'   => $message,
        ], $_HTTP_CODE);
    }

    

    function responseJSONFalse($is_commit_db = false, $message = "Lỗi", $data = null, $_HTTP_CODE = JsonResponse::HTTP_OK) {
        if($is_commit_db) {
            DB::rollBack();
        }
        if ($data === null) {
            return response()->json([
                'success'   => false,
                'message'   => $message
            ], $_HTTP_CODE);
        }

        return response()->json([
            'success'   => false,
            'message'   => $message,
            $data
        ], $_HTTP_CODE);
    }
}
