<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePackageDetailRequest;
use App\Models\PackageDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageDetailController extends CoreController
{
    //

    public $data = [];

    public function __construct() {

    }

    // add or edit package detail
    public function create(CreatePackageDetailRequest $request) {
        $package_detail_id = $request->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if ( ! $package_detail_id ) { 
            $check_valid_time = PackageDetail::where(function($q) use($start_date, $end_date){
                                                    $q->whereDate('end_date','>=', date('Y-m-d H:i:s', strtotime($start_date)))
                                                        ->whereDate('end_date','<=', date('Y-m-d H:i:s', strtotime($end_date)));
                                                })
                                                ->orWhere(function($q) use($start_date, $end_date){
                                                    $q->whereDate('start_date','<=', date('Y-m-d H:i:s', strtotime($start_date)))
                                                        ->whereDate('start_date','>=', date('Y-m-d H:i:s', strtotime($end_date)));
                                                })
                                                ->where('is_deleted', 0)->get();
            
            if (count($check_valid_time) > 0) {
                return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Đã có gói cước hoạt động trong thời gian này");
            }
        }
        $data_package_detail = [
            'package_id'        => $request->package_id,
            "start_date"        => $request->start_date,
            "end_date"          => $request->end_date,
            "price"             => $request->price,
            "price_vat"         => $request->price_vat,
            "decision_number"   => $request->decision_number,
            "is_deleted"        => 0,
        ];

        DB::beginTransaction();
        try {
            $message = "Cập nhật địa chỉ thành công";
            if ( ! $package_detail_id ) {
                DB::table('package_detail')->insert($data_package_detail);
                $message = "Tạo mới địa chỉ thành công";
            } else {
                
                // $check
                DB::table('package_detail')->where('id', $package_detail_id)->update($data_package_detail);
            }
            
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, $message);
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server. Tạo chi tiết gói cước lỗi");
        }
    }

    public function view_listPackageDetail(Request $request) {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $package_id = $request->package_id;

        // $search_value = mb_strtolower($request->search_value);

        $data = PackageDetail::where('package_id', $package_id)
                            ->where('is_deleted', 0)
                            ->orderBy('id', 'desc');
        $count = $data->count();
        // $data_package = $data->offset($offset)->limit($limit)->get();
        $data_package = $data->get();
        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu thành công",
            'rows'      => $data_package,
            'total'     => $count,
        ], 200);
    }

    public function delete(Request $request) {
        $table_name = 'package_detail';
        $id = $request->id;

        $package_id = $request->package_id;
        if ( ! $id || ! $package_id) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thiếu thông tin cần xóa!");
        }
        $address_info = PackageDetail::where('id', $id)->where('package_id', $package_id)->where('is_deleted', 0)->first();

        if (!$address_info) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin chi tiết gói cước không tồn tại!");
        }

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_deleted'   => 1]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Xóa thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!. Xóa chi tiết gói cước bị lỗi!");
        }
        
    }
}
