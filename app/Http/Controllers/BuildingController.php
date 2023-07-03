<?php

namespace App\Http\Controllers;

use App\Exports\BuildingExport;
use App\Http\Requests\CreateSingleBuildingRequest;
use App\Http\Requests\UpdateSingleBuildingRequest;
use App\Imports\BuildingImport;
use App\Models\Building;
use App\Models\CustomerAddress;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BuildingController extends CoreController
{
    public $data = [];
    public function __construct()
    {

    }

    public function export(Request $request) {
        $search_value = strtolower($request->search_value);
        $status = $request->status;

        $data = Building::where('is_deleted', 0)
                        ->where(function ($query) use($search_value) {
                            $query->when($search_value, function ($query) use ($search_value) {
                                $query->where(DB::raw("LOWER(building_name)"), 'LIKE', "%" . $search_value . "%")
                                    ->orWhere(DB::raw('LOWER(building_code)'), 'LIKE', '%' . $search_value . '%');
                            });
                        })
                        ->when($status, function ($query) use ($status) {
                            $query->where('is_active', $status);
                        })
                        ->orderBy('id', 'desc')
                        ->get();

        $params = ['data' => $data, 'template' => 'exports.buildings'];
        $filename = 'buildings_at_'.date('H_i_s_d_m_Y').'.xlsx';

        return Excel::download(new BuildingExport($params), $filename);
    }

    public function view_index(Request $request)
    {
        $listPartner = DB::table('partners')->where('is_deleted', '0')->get();
        $this->data['list_partner'] = $listPartner;
        $this->data['template_import'] = url('templates/TemplateBuilding.xlsx');
        return view('buildings.list', $this->data);
    }

    public function view_map()
    {
        return view('buildings.map');
    }

    public function view_list_map()
    {
        $list_map = Building::with('jobs')->where('is_deleted', '0')->get();
        $data = [];
        if ($list_map) {
            foreach ($list_map as $item) {
                $icon_link = url('/uploads/icons/available.png');

                // foreach ($item->jobs as $key => $job_item) {
                //     if ($job_item->job_type == 1 && $job_item->status == Job::COMPLETED){
                //         $icon_link = url('/uploads/icons/available.png');
                //     }
                // }

                array_push($data, [
                    array(
                        'lat'       => floatval($item->latitude),
                        'lng'       => floatval($item->longitude),
                        // 'icon_link' => $icon_link
                    ),
                    $icon_link,
                    '<b>Tòa nhà ' . $item->building_name . '(' . $item->building_code . ')</b><br/>Kinh độ: ' . $item->latitude . '<br/>Vĩ độ: ' . $item->longitude . '<br/>Địa chỉ: ' . $item->address . '<br/>Liên hệ: ' . $item->contact_name . ' - ' . $item->contact_phone
                ]);
            }
        }

        // get customer address
        $list_customer_address_map = CustomerAddress::with('customer', 'jobs')->where('is_deleted', 0)->whereNotNull('longitude')->whereNotNull('latitude')->whereHas('customer', function($q) {
            $q->where('is_deleted', 0);
        })->get();
        if (count($list_customer_address_map) > 0) {
            foreach ($list_customer_address_map as $item) {
                $icon_link = url('/uploads/icons/unavailable.png');

                foreach ($item->jobs as $key => $job_item) {
                    if ($job_item->job_type == 1 && $job_item->status == Job::COMPLETED){
                        $icon_link = url('/uploads/icons/available.png');
                        break;
                    }
                }
                array_push($data, [
                    array(
                        'lat'       => floatval($item->latitude),
                        'lng'       => floatval($item->longitude),
                    ),
                    $icon_link,
                    'Kinh độ: ' . $item->latitude . '<br/>Vĩ độ: ' . $item->longitude . '<br/>Địa chỉ: ' . $item->address . '<br/>Liên hệ: ' . $item->customer->firstname . ' - ' . $item->customer->phone
                ]);
            }
        }
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function create(CreateSingleBuildingRequest $request)
    {
        DB::beginTransaction();
        try {
            $data_buildings = [
                "building_code"             => $request->building_code,
                "building_name"             => $request->building_name,
                "building_company"          => $request->building_company,
                "address"                   => $request->building_address,
                "longitude"                 => $request->building_longitude,
                "latitude"                  => $request->building_latitude,
                "contact_name"              => $request->contact_name,
                "contact_phone"             => $request->contact_phone,
                "partner_id"                => $request->partner_id,
                "cooperate_type"            => $request->cooperate_type,
                "percent_share"             => $request->percent_share,
                "is_active"                 => $request->is_active,
                "created_at"                => date('Y-m-d h:i:s'),
                "created_by"                => Auth::user()->id
            ];

            DB::table('buildings')->insert($data_buildings);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Thêm mới thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function update(UpdateSingleBuildingRequest $request)
    {
        $building_id = $request->id;
        $data_building = [
            "building_code"             => $request->building_code,
            "building_name"             => $request->building_name,
            "building_company"          => $request->building_company,
            "address"                   => $request->building_address,
            "longitude"                 => $request->building_longitude,
            "latitude"                  => $request->building_latitude,
            "contact_name"              => $request->contact_name,
            "contact_phone"             => $request->contact_phone,
            "partner_id"                => $request->partner_id,
            "cooperate_type"            => $request->cooperate_type,
            "percent_share"             => $request->percent_share,
            "is_active"                 => $request->is_active,
            "updated_by"                => Auth::user()->id,
            "updated_at"                => date('Y-m-d h:i:s')
        ];
        // dd($data_building);

        DB::beginTransaction();
        try {
            DB::table('buildings')->where('id', $building_id)->update($data_building);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Cập nhật thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server");
        }
    }

    public function delete(Request $request)
    {
        $table_name = 'buildings';
        $id = $request->id;
        $building = Building::where('id', $id)->first();

        if (!$building) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin toà nhà không tồn tại");
        }

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_deleted'   => 1]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Xóa thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function update_data(Request $request)
    {
        $table_name = 'buildings';
        $id = $request->id;
        $building = Building::where('id', $id)->first();

        if (!$building) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin toà nhà không tồn tại");
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

    public function view(Request $request)
    {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $search_value = strtolower($request->search_value);
        // $search_value = str_replace(' ', '', mb_strtolower($request->search_value));
        $status = $request->status;

        $data = Building::with('contract_await_sign', 'contract_active', 'contract_expired')
                            ->where('is_deleted', 0)
                            ->where(function ($query) use($search_value) {
                                $query->when($search_value, function ($query) use ($search_value) {
                                    $query->where(DB::raw("LOWER(building_name)"), 'LIKE', "%" . $search_value . "%")
                                        ->orWhere(DB::raw('LOWER(building_code)'), 'LIKE', '%' . $search_value . '%');
                                });
                            })
                            ->when($status, function ($query) use ($status) {
                                $query->where('is_active', $status);
                            })
                            ->orderBy('id', 'desc');

        $count = $data->count();
        // $data_service = $data->offset($offset)->limit($limit)->get();
        $data_buildings = $data->get();
        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu thành công",
            'rows'      => $data_buildings,
            'total'     => $count,
        ], 200);
    }

    public function import(Request $request)
    {
        try {
            $import = new BuildingImport();
            $file = $request->file('fileExcel');
            Excel::import($import, $file);

            // Lấy mảng các hàng lỗi
            $rows = $import->row_import;

            // Trả lại mảng các hàng lỗi cho client
            // return response()->json(['errors' => $errors]);
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

    public function update_excel(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = json_decode($request->data, true);
            if (!$data || count($data) <= 0) {
                return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Không có dữ liệu để import");
            }
            $data_arr = [];
            foreach ($data as $item) {
                $partner_code = $item['8'];
                $check_partner_info = DB::table('partners')->where('partner_code', $partner_code)->where('is_deleted', "0")->first();
                if (!$check_partner_info) {
                    continue;
                }
                $partner_id = $check_partner_info->id;
                $data_arr[] = [
                    "building_code"             => $item['0'],
                    "building_name"             => $item['1'],
                    "building_company"          => $item['2'],
                    "address"                   => $item['3'],
                    "longitude"                 => $item['4'],
                    "latitude"                  => $item['5'],
                    "contact_name"              => $item['6'],
                    "contact_phone"             => $item['7'],
                    "partner_id"                => $partner_id,
                    "cooperate_type"            => $item['9'],
                    "percent_share"             => $item['10'],
                    "is_active"                 => 1,
                    "is_deleted"                => 0,
                    "created_at"                => date('Y-m-d h:i:s'),
                    "created_by"                => Auth::user()->id
                ];
            }
            DB::table('buildings')->insert($data_arr);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Import dữ liệu thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }
}
