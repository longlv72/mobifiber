<?php

namespace App\Http\Controllers;

use App\Exports\DeviceExport;
use App\Http\Requests\CreateSingleDeviceRequest;
use App\Http\Requests\UpdateSingleDeviceRequest;
use App\Imports\DeviceImport;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DeviceController extends CoreController
{

    public $data = [];

    public function __construct() {

    }

    public function export(Request $request) {
        $search_value = strtolower($request->search_value);
        $device_status = $request->device_status;
        // dd($request->all());
        $data = Device::where('is_deleted', 0)
                        ->where(function($q) use($search_value) {
                            $q->when($search_value, function($query) use($search_value){
                                $query->where(DB::raw('lower(device_name)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(device_code)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(serial)'), 'like', '%'.$search_value.'%');
                            });
                        })
                        ->when($device_status, function($query) use($device_status){
                            $query->where('is_active', $device_status);
                        })
                        ->orderBy('id', 'desc')
                        ->get();
        $params = ['data' => $data, 'template' => 'exports.devices'];
        $filename = 'devices_at_'.date('H_i_s_d_m_Y').'.xlsx';
        
        return Excel::download(new DeviceExport($params), $filename);
    }

    public function view(Request $request) {
        $this->data['template_import'] = url('templates/TemplateDevice.xlsx');
        return view('devices.list', $this->data);
    }

    public function create(CreateSingleDeviceRequest $request) {
        DB::beginTransaction();
        try {
            $data_devices = [
                "device_code"               => $request->device_code,
                "device_name"               => $request->device_name,
                "serial"                    => $request->serial_number,
                "is_active"                 => $request->device_status,
                "created_at"                => date('Y-m-d H:i:s'),
                "created_by"                => Auth::user()->id
            ];
            DB::table('devices')->insert($data_devices);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Thêm mới thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function update(UpdateSingleDeviceRequest $request) {
        $device_id = $request->id;
        $data_device = [
            "device_code"               => $request->device_code,
            "device_name"               => $request->device_name,
            "serial"                    => $request->serial_number,
            "is_active"                 => $request->device_status,
            "updated_by"                => Auth::user()->id,
            "updated_at"                => date('Y-m-d H:i:s')
        ];

        DB::beginTransaction();
        try {
            DB::table('devices')->where('id', $device_id)->update($data_device);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Cập nhật thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server");
        }
    }

    public function delete(Request $request) {
        $table_name = 'devices';
        $id = $request->id;
        $device = Device::where('id', $id)->first();

        if (!$device) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin thiết bị không tồn tại");
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

    public function update_status(Request $request) {
        $table_name = 'devices';
        $id = $request->id;
        $device = Device::where('id', $id)->first();

        if (!$device) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin thiết bị không tồn tại");
        }
        $is_active = $request->is_active == 1 ? 2 : 1;

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_active'   => $is_active]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Đổi trạng thái thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function view_list(Request $request) {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $search_value = strtolower($request->search_value);
        $device_status = $request->device_status;
        
        $data = Device::where('is_deleted', 0)
                        ->where(function($q) use($search_value) {
                            $q->when($search_value, function($query) use($search_value){
                                $query->where(DB::raw('lower(device_name)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(device_code)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(serial)'), 'like', '%'.$search_value.'%');
                            });
                        })
                        ->when($device_status, function($query) use($device_status){
                            $query->where('is_active', $device_status);
                        })
                        ->orderBy('id', 'desc');
        $count = $data->count();
        // $data_service = $data->offset($offset)->limit($limit)->get();
        $data_devices = $data->get();
        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu thành công",
            'rows'      => $data_devices,
            'total'     => $count,
        ], 200);
    }

    public function import(Request $request) {
        try {
            $import = new DeviceImport();
            $file = $request->file('fileExcel');
            Excel::import($import, $file);

            // Lấy mảng các hàng lỗi
            $rows = $import->row_import;

            // Trả lại mảng các hàng lỗi cho client
            return $this->responseJSONSuccess($this::IS_NOT_COMMIT_DB, "Ok", [
                'rows'          => $rows,
                'total'         => count($rows),
                'totalError'    => $import->countError,
                'row_errors'    => $import->row_errors_number,
            ]);
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_NOT_ROLL_BACK_DB, "Lỗi tệp tin. Vui lòng tải lại tệp tin để thử lại");
            // return $this->responseJSONFalse($this::IS_NOT_ROLL_BACK_DB, $th->getMessage() . ' - line: ' . $th->getLine() . ' - file: ' . $th->getFile());
        }
    }

    public function update_data(Request $request) {
        $data = json_decode($request->data, true);
        if (!$data || count($data) <= 0) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Không có dữ liệu để import");
        }
        $data_arr = [];
        foreach ($data as $item) {
            $data_arr[] = [
                'device_code'     => $item['0'],
                'device_name'     => $item['1'],
                'serial'          => $item['2'],
                'is_deleted'      => 0,
                'is_active'       => 1,
                'created_at'      => date('Y-m-d H:i:s'),
                'created_by'      => Auth::user()->id
            ];
        }

        DB::beginTransaction();
        try {
            DB::table('devices')->insert($data_arr);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Import dữ liệu thành công");
        } catch (\Throwable $th) {
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

}
