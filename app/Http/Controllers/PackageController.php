<?php

namespace App\Http\Controllers;

use App\Exports\PackageExport;
use App\Http\Requests\CreateSinglePackageRequest;
use App\Http\Requests\UpdateSinglePackageRequest;
use App\Imports\PackageImport;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PackageController extends CoreController
{
    public $data = [];

    public function __construct()
    {
    }

    public function view(Request $request)
    {
        $this->data['template_import'] = url('templates/TemplatePackage.xlsx');
        return view('packages.list', $this->data);
    }

    public function export(Request $request) {
        $status = $request->status;
        $search_value = mb_strtolower($request->search_value);
        
        $data = Package::where('is_deleted', 0)
                        ->where(function($q) use($search_value) {
                            $q->when($search_value, function ($query) use ($search_value) {
                                $query->where(DB::raw('lower(package_name)'), 'LIKE', '%' . $search_value . '%')
                                        ->orWhere(DB::raw('lower(package_code)'), 'LIKE', '%' . $search_value . '%')
                                        ->orWhere(DB::raw('lower(decision)'), 'LIKE', '%' . $search_value . '%');
                            });
                        })
                        ->when($status, function ($query) use ($status) {
                            $query->where('is_active', $status);
                        })
                            ->orderBy('id', 'desc')
                        ->get();
                            
        $params = ['data' => $data, 'template' => 'exports.packages'];
        $filename = 'packages_at_'.date('H_i_s_d_m_Y').'.xlsx';
        
        return Excel::download(new PackageExport($params), $filename);
    }

    public function create(CreateSinglePackageRequest $request)
    {
        if($request->package_price > $request->package_price_vat){
            return $this->responseJSONFalse(false, "Giá trước thuế  không được lớn hơn giá sau thuế");
        }
        DB::beginTransaction();
        try {
            $data_package = [
                "package_name"      => $request->package_name,
                "package_code"      => $request->package_code,
                "time_used"         => $request->time_used,
                "promotion_time"    => $request->promotion_time,
                "decision"          => $request->descision,
                "is_active"         => $request->active_package,
                "created_at"        => date('Y-m-d H:i:s'),
                "created_by"        => Auth::user()->id,
            ];
            DB::table('packages')->insert($data_package);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Thêm mới thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }
    public function update(UpdateSinglePackageRequest $request)
    {
        $package_id = $request->id;
        $data_package = [
            "package_name"      => $request->package_name,
            "package_code"      => $request->package_code,
            "time_used"         => $request->time_used,
            "promotion_time"    => $request->promotion_time,
            "decision"          => $request->descision,
            "is_active"         => $request->active_package,
            "updated_at"        => date('Y-m-d H:i:s'),
            "updated_by"        => Auth::user()->id
        ];

        // dd($data_package);

        DB::beginTransaction();
        try {
            DB::table('packages')->where('id', $package_id)->update($data_package);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Cập nhật thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server");
        }
    }
    public function delete(Request $request)
    {
        $table_name = 'packages';
        $id = $request->id;
        $package = Package::where('id', $id)->first();

        if (!$package) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin gói cước không tồn tại");
        }

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_deleted'   => 1]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Xóa thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }
    public function update_status(Request $request)
    {
        $table_name = 'packages';
        $id = $request->id;
        $package = Package::where('id', $id)->first();

        if (!$package) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin gói cước không tồn tại");
        }
        $is_active = $request->is_active == 1 ? 2 : 1;

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_active'   => $is_active]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Đổi trạng thái thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function view_list(Request $request)
    {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $status = $request->status;
        $search_value = mb_strtolower($request->search_value);
        // dd($status);
        $data = Package::with('package_detail')
                            ->where('is_deleted', 0)
                            ->where(function($q) use($search_value) {
                                $q->when($search_value, function ($query) use ($search_value) {
                                    $query->where(DB::raw('lower(package_name)'), 'LIKE', '%' . $search_value . '%')
                                            ->orWhere(DB::raw('lower(package_code)'), 'LIKE', '%' . $search_value . '%')
                                            ->orWhere(DB::raw('lower(decision)'), 'LIKE', '%' . $search_value . '%');
                                });
                            })
                            ->when($status, function ($query) use ($status) {
                                $query->where('is_active', $status);
                            })
                            ->orderBy('id', 'desc');
        $count = $data->count();
        // $data_service = $data->offset($offset)->limit($limit)->get();
        $data_package = $data->get();
        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu thành công",
            'rows'      => $data_package,
            'total'     => $count,
        ], 200);
    }
    public function import(Request $request)
    {
        try {
            $import = new PackageImport();
            $file = $request->file('fileExcel');
            Excel::import($import, $file);

            // Lấy mảng các hàng lỗi
            $rows = $import->row_import;

            // Trả lại mảng các hàng lỗi cho client
            // return response()->json(['errors' => $errors]);
            return $this->responseJSONSuccess($this::IS_NOT_COMMIT_DB, "190", [
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
    public function update_data(Request $request)
    {
        $data = json_decode($request->data, true);

        if (!$data || count($data) <= 0) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Không có dữ liệu để import");
        }
        $data_arr = [];
        foreach ($data as $item) {
            $data_arr[] = [
                'package_name'        => $item['0'],
                'package_code'        => $item['1'],
                'prices'              => $item['2'],
                'prices_vat'          => $item['3'],
                'time_used'           => $item['4'],
                'promotion_time'      => $item['5'],
                'decision'            => $item['6'],
                'is_active'           => 1,
                'is_deleted'          => 0,
            ];
        }

        DB::beginTransaction();
        try {
            DB::table('packages')->insert($data_arr);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Import dữ liệu thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }
}
