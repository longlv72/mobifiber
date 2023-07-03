<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveAddressRequest;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerAddressController extends CoreController
{
    public $data = [];

    public function __construct() {

    }

    public function update(SaveAddressRequest $request) {
        $address_id = $request->id;
        $address = $request->address;
        $longitude = $request->longitude;
        $latitude = $request->latitude;
        $customer_id = $request->customer_id;
        $data = [
            'address'       => $address,
            'longitude'     => $longitude,
            'latitude'      => $latitude,
            'customer_id'   => $customer_id,
        ];
        DB::beginTransaction();
        try {
            $message = "Cập nhật địa chỉ thành công";
            if ( ! $address_id ) {
                $data = array_merge($data, [
                    'created_at'    => date('Y-m-d H:i:s'),
                    'created_by'    => Auth::user()->id,
                    'is_deleted'    => 0
                ]);
                DB::table('customer_addresses')->insert($data);
                $message = "Tạo mới địa chỉ thành công";
            } else {
                $data = array_merge($data, [
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'updated_by'    => Auth::user()->id
                ]);
                DB::table('customer_addresses')->where('id', $address_id)->update($data);
            }
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, $message);
        } catch (\Throwable $th) {
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!. Lưu thông tin địa chỉ lỗi!");
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function view(Request $request) {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $customer_id = $request->customer_id;

        if ( ! $customer_id ) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thiếu thông tin khách hàng");
        }

        $data = CustomerAddress::where('is_deleted', 0)->where('customer_id', $customer_id)
                                    ->orderBy('id', 'desc');

        $count = $data->count();
        // $data_service = $data->offset($offset)->limit($limit)->get();
        $data_customer_address = $data->get();
        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu thành công",
            'rows'      => $data_customer_address,
            'total'     => $count,
        ], 200);
    }

    public function delete(Request $request) {
        $table_name = 'customer_addresses';
        $id = $request->id;
        $customer_id = $request->customer_id;
        if ( ! $id || ! $customer_id) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thiếu thông tin cần xóa!");
        }
        $address_info = CustomerAddress::where('id', $id)->where('customer_id', $customer_id)->first();

        if (!$address_info) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin địa chỉ không tồn tại!");
        }

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_deleted'   => 1]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Xóa thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }
}
