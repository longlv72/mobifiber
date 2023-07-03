<?php

namespace App\Http\Controllers;

use App\Exports\ContractExport;
use App\Http\Requests\CreateSingleContractRequest;
use App\Http\Requests\UpdateSingleContractRequest;
use App\Imports\ContractImport;
use App\Models\Building;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Job;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ContractController extends CoreController
{

    public $data = [];
    public function __construct()
    {

    }

    public function export(Request $request) {
        $type_cooperate = $request->type_cooperate;
        $contract_status = $request->contract_status;
        $search_value = mb_strtolower($request->search_value);

        $data = Contract::with('package', 'device', 'partner', 'customer', 'building')
                            ->where('is_deleted', 0)
                            ->when($type_cooperate, function ($query) use ($type_cooperate) {
                                $query->where('type_cooperate', $type_cooperate);
                            })
                            ->when($contract_status, function ($query) use ($contract_status) {
                                $query->where('status', $contract_status);
                            })
                            ->where(function($query) use($search_value) {
                                $query->when($search_value, function ($query) use ($search_value) {
                                        $query->where(DB::raw('lower(bill_code)'), 'LIKE', "%".$search_value."%")->orWhere(DB::raw('lower(code)'), 'LIKE', "%".$search_value."%")
                                            ->orWhereHas('customer', function($q) use($search_value) {
                                                $q->where('is_deleted', 0)
                                                    ->where(function($q) use($search_value) {
                                                        $q->where(DB::raw("LOWER(CONCAT(CONCAT(last_name, ' '), firstname))"), 'LIKE', "%" . $search_value . "%")
                                                            ->orWhere(DB::raw("LOWER(phone)"), 'LIKE', "%" . $search_value . "%")
                                                            ->orWhere(DB::raw("LOWER(email)"), 'LIKE', "%" . $search_value . "%");
                                                    });
                                            })
                                            ->orWhereHas('building', function($q) use($search_value) {
                                                $q->where(DB::raw("LOWER(building_name)"), 'LIKE', "%" . $search_value . "%")
                                                ->orWhere(DB::raw("LOWER(REPLACE(CONCAT(CONCAT(building_name, '-'), address), ' ', ''))"), 'LIKE', "%" . $search_value . "%")
                                                ->where('is_deleted', 0);
                                            })
                                            ->orWhereHas('partner', function($q) use($search_value) {
                                                $q->where(DB::raw("LOWER(email)"), 'LIKE', "%" . $search_value . "%")
                                                ->orWhere(DB::raw("LOWER(phone)"), 'LIKE', "%" . $search_value . "%")
                                                ->where('is_deleted', 0);
                                            })
                                            ->orWhereHas('package', function($q) use($search_value) {
                                                $q->where(DB::raw("LOWER(package_name)"), 'LIKE', "%" . $search_value . "%")
                                                ->where('is_deleted', 0);
                                            });
                                });
                            })
                            ->orderBy('id', 'desc')
                            ->get();

        $params = ['data' => $data, 'template' => 'exports.contracts'];
        $filename = 'contracts_at_'.date('H_i_s_d_m_Y').'.xlsx';

        return Excel::download(new ContractExport($params), $filename);
    }

    public function view_index(Request $request)
    {
        $this->data['list_packages'] = getAllPackage();
        $this->data['list_devices'] = getAllDevices();
        $this->data['list_partner'] = getAllPartners();
        $this->data['list_employees'] = getAllEmployees();

        $this->data['getAllCustomers'] = getAllCustomers();
        $this->data['getAllEmployees'] = getEngineerEmployees();
        $this->data['getAllBuildings'] = getAllBuildings();
        $this->data['jobTypeList'] = getJobType();
        $this->data['allPartners'] = getAllPartners();
        $this->data['jobReasonList'] = GetReasonJobList(Job::EQUIPMENT_INSTALLATION);
        $this->data['EQUIPMENT_INSTALLATION'] = Job::EQUIPMENT_INSTALLATION;
        $this->data['list_customer'] = Customer::whereHas('jobs', function($q) {
            $q->where('job_type', Job::SURVEY)->where('status', Job::COMPLETED)->where('is_deleted', 0);
        })
        ->get();
        $this->data['template_import'] = url('templates/TemplateContract.xlsx');
        return view('constracts.list', $this->data);
    }

    public function create(CreateSingleContractRequest $request)
    {
        DB::beginTransaction();
        try {
            // dd($request->start_date_package);
            $data_contracts = [
                "code"                      => $request->contract_code, // mã hợp đồng
                "type_cooperate"            => $request->type_cooperate, // Loại hình hợp tác
                "package_id"                => $request->package_id, // mã gói cước
                "start_date_package"        => $request->start_date_package, // ngày bắt đầu gói cước
                "device_id"                 => $request->device_id, // thiết bị
                "bill_code"                 => $request->bill_code, // mã hóa đơn
                "bill_date"                 => $request->bill_date ?? "", // ngày hóa đơn
                "partner_id"                => $request->partner_id, // đối tác hạ tầng
                "buildings_id"              => $request->building_id, // tòa nhà
                "customer_id"               => $request->customer_id, // khách hàng
                "address_id"                => $request->address_id, // địa chỉ khách hàng
                "status"                    => $request->contract_status, // trạng thái hợp đồng
                "shop_code"                 => $request->shopcode, // mã cửa hàng
                "customer_code"             => $request->customer_code, // mã khách hàng
                "employee_id"               => $request->employee_id, // mã x viên
                "created_at"                => date('Y-m-d H:i:s'),
                "created_by"                => Auth::user()->id
            ];

            $package_id = $request->package_id;
            $package_info = Package::with('package_detail')->where('id', $package_id)->first();
            if ( $package_info ) {
                $data_package_history = [
                    // 'package_price'             => $package_info->prices,
                    'package_time_used'         => $package_info->time_used,
                    'package_time_promotion'    => $package_info->promotion_time,
                    "bill_prices"               => ($package_info->package_detail && $package_info->package_detail->price) ? $package_info->package_detail->price : "",
                    "bill_prices_vat"           => ($package_info->package_detail && $package_info->package_detail->price_vat) ? $package_info->package_detail->price_vat : "",
                ];
                $data_contracts = array_merge($data_contracts, $data_package_history);
            }

            $building_id = $request->building_id;
            $building_info = Building::where('id', $building_id)->first();
            if ( $building_info ) {
                $data_contracts = array_merge($data_contracts, [
                    'percent_share'             => $building_info->percent_share,
                ]);
            }
            // dd($data_contracts);

            DB::table('contracts')->insert($data_contracts);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Thêm mới thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function update(UpdateSingleContractRequest $request)
    {
        $contract_id = $request->id;
        $data_contract = [
            "code"                      => $request->contract_code, // mã hợp đồng
            "type_cooperate"            => $request->type_cooperate, // loại hình hợp tác
            "package_id"                => $request->package_id,
            "start_date_package"        => $request->start_date_package,
            "device_id"                 => $request->device_id,
            "bill_code"                 => $request->bill_code,
            "bill_date"                 => $request->bill_date,
            "partner_id"                => $request->partner_id,
            "buildings_id"              => $request->building_id,
            "customer_id"               => $request->customer_id, // khách hàng
            "address_id"                => $request->address_id, // địa chỉ khách hàng
            "status"                    => $request->contract_status, // trạng thái hợp đồng
            "shop_code"                 => $request->shopcode ?? null, // mã cửa hàng
            "customer_code"             => $request->customer_code, // mã khách hàng
            "employee_id"               => $request->employee_id, // mã x viên
            "updated_by"                => Auth::user()->id,
            "updated_at"                => date('Y-m-d H:i:s')
        ];

        DB::beginTransaction();
        try {

            $package_id = $request->package_id;
            $package_info = Package::with('package_detail')->where('id', $package_id)->first();
            if ( $package_info ) {

                $data_package_history = [
                    'package_time_used'         => $package_info->time_used,
                    'package_time_promotion'    => $package_info->promotion_time,
                    "bill_prices"               => ($package_info->package_detail && $package_info->package_detail->price) ? $package_info->package_detail->price : "",
                    "bill_prices_vat"           => ($package_info->package_detail && $package_info->package_detail->price_vat) ? $package_info->package_detail->price_vat : "",
                ];
                $data_contract = array_merge($data_contract, $data_package_history);
            }

            $building_id = $request->building_id;
            $building_info = Building::where('id', $building_id)->first();
            if ( $building_info ) {
                $data_contract = array_merge($data_contract, [
                    'percent_share'             => $building_info->percent_share,
                ]);
            }
            DB::table('contracts')->where('id', $contract_id)->update($data_contract);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Cập nhật thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server");
        }
    }

    public function delete(Request $request)
    {
        $table_name = 'contracts';
        $id = $request->id;
        $contract = Contract::with('jobs')->where('id', $id)->first();

        if (!$contract) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin hợp đồng không tồn tại");
        }

        if ($contract->jobs && count($contract->jobs) > 0 ) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Hợp đồng này đang tồn tại công việc liên quan. Không được xóa");
        }

        dd($contract->jobs);

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
        $table_name = 'contracts';
        $id = $request->id;
        $contract = Contract::where('id', $id)->first();

        if (!$contract) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin hợp đồng không tồn tại");
        }
        $is_active = $request->is_active == 1 ? 2 : 1;

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['status'   => $is_active]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Đổi trạng thái thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function view_list(Request $request){
        $data = Building::where('partner_id', $request->id)
                        ->where('is_deleted', 0)
                        ->get();
        return response()->json([
            "success"   => true,
            'data'      => $data
        ], 200);
    }

    public function view(Request $request)
    {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $type_cooperate = $request->type_cooperate;
        $contract_status = (int) $request->contract_status;
        // dd($contract_status);
        $search_value = mb_strtolower($request->search_value);

        $data = Contract::with(['package', 'device', 'partner', 'customer', 'building', 'jobs'])
                            ->where('is_deleted', 0)
                            ->when($type_cooperate, function ($query) use ($type_cooperate) {
                                $query->where('type_cooperate', $type_cooperate);
                            })
                            ->when($contract_status != 0, function ($query) use ($contract_status) {
                                $query->where('status', $contract_status);
                            })
                            ->where(function($query) use($search_value) {
                                $query->when($search_value, function ($query) use ($search_value) {
                                        $query->where(DB::raw('lower(bill_code)'), 'LIKE', "%".$search_value."%")->orWhere(DB::raw('lower(code)'), 'LIKE', "%".$search_value."%")
                                            ->orWhereHas('customer', function($q) use($search_value) {
                                                $q->where('is_deleted', 0)
                                                    ->where(function($q) use($search_value) {
                                                        $q->where(DB::raw("LOWER(CONCAT(CONCAT(last_name, ' '), firstname))"), 'LIKE', "%" . $search_value . "%")
                                                            ->orWhere(DB::raw("LOWER(phone)"), 'LIKE', "%" . $search_value . "%")
                                                            ->orWhere(DB::raw("LOWER(email)"), 'LIKE', "%" . $search_value . "%");
                                                    });
                                            })
                                            ->orWhereHas('building', function($q) use($search_value) {
                                                $q->where(DB::raw("LOWER(building_name)"), 'LIKE', "%" . $search_value . "%")
                                                ->orWhere(DB::raw("LOWER(building_code)"), 'LIKE', "%" . $search_value . "%")
                                                ->orWhere(DB::raw("LOWER(REPLACE(CONCAT(CONCAT(building_name, '-'), address), ' ', ''))"), 'LIKE', "%" . $search_value . "%")
                                                ->where('is_deleted', 0);
                                            })
                                            ->orWhereHas('partner', function($q) use($search_value) {
                                                $q->where(DB::raw("LOWER(email)"), 'LIKE', "%" . $search_value . "%")
                                                ->orWhere(DB::raw("LOWER(phone)"), 'LIKE', "%" . $search_value . "%")
                                                ->where('is_deleted', 0);
                                            })
                                            ->orWhereHas('package', function($q) use($search_value) {
                                                $q->where(DB::raw("LOWER(package_name)"), 'LIKE', "%" . $search_value . "%")
                                                ->where('is_deleted', 0);
                                            });
                                });
                            })
                            ->orderBy('id', 'desc');
        $count = $data->count();
        $data_contracts = $data->offset($offset)->limit($limit)->get();
        // $data_contracts = $data->get();

        foreach ($data_contracts as $key => &$value) {
            $customer = $value->customer;
            if ( $customer && ( $customer->cccd_front != null || $customer->cccd_backside != null )) {
                $customer->cccd_front_url = url('/uploads/cccd/'. $customer->cccd_front);
                $customer->cccd_backside_url = url('/uploads/cccd/'. $customer->cccd_backside);
            }

            $value->finished_servey = 1;
            if ($value->jobs) {
                $jobs = $value->jobs;
                foreach ($jobs as &$job_item) {
                    if ($job_item->job_type == Job::SURVEY && $job_item->status == Job::COMPLETED) {
                        $value->finished_servey = 2;

                        $value->job_customer_id = $job_item->customer_id;
                        $value->job_building_id = $job_item->building_id;
                        $value->job_partner_id = $job_item->partner_id;
                        $value->job_address_id = $job_item->address_id;
                        $value->job_id = $job_item->id;
                        // break;
                    }
                    // dd($value);
                    if ($job_item->job_type == Job::EQUIPMENT_INSTALLATION) {
                        $value->finished_servey = 1;
                    }
                }
            }
        }

        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu thành công",
            'rows'      => $data_contracts,
            'total'     => $count,
        ], 200);
    }

    public function import(Request $request)
    {
        try {
            $import = new ContractImport();
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
                // handle package_id
                $package_code = $item['1'];
                $package_info_by_code = DB::table('packages')->where('package_code', $package_code)->where('is_deleted', 0)->first();
                if (!$package_info_by_code) continue;
                $package_id = $package_info_by_code->id;
                $package_price = $package_info_by_code->prices;
                $package_price_vat = $package_info_by_code->prices_vat;
                $package_time_used = $package_info_by_code->time_used;
                $package_promotion_time = $package_info_by_code->promotion_time;

                //handle device_code
                $device_code = $item['3'];
                $device_info_by_code = DB::table('devices')->where('device_code', $device_code)->where('is_deleted', 0)->first();
                if (!$device_info_by_code) continue;
                $device_id = $device_info_by_code->id;

                //handle partner_code
                $partner_code = $item['6'];
                $partner_info_by_code = DB::table('partners')->where('partner_code', $partner_code)->where('is_deleted', 0)->first();
                if (!$partner_info_by_code) continue;
                $partner_id = $partner_info_by_code->id;

                //handle building_code
                $building_code = $item['7'];
                $building_info_by_code = DB::table('buildings')->where('building_code', $building_code)->where('is_deleted', 0)->first();
                if (!$building_info_by_code) continue;
                $building_id = $building_info_by_code->id;
                $percent_share = $building_info_by_code->percent_share;

                //handle customer_code
                $customer_code = $item['9'];
                $customer_info_by_code = DB::table('customers')->where('code', $customer_code)->where('is_deleted', 0)->first();
                if (!$customer_info_by_code) continue;
                $customer_id = $customer_info_by_code->id;

                //handle user employee
                $employee_username = $item['12'];
                $employee_info_by_username = DB::table('users')->where('username', $employee_username)->where('is_deleted', 0)->first();
                if ( ! $employee_info_by_username ) continue;
                $employee_id = $employee_info_by_username->id;

                $data_arr[] = [
                    "code"                      => $item['0'],
                    "package_id"                => $package_id, //1
                    "start_date_package"        => $item['2'],
                    "device_id"                 => $device_id, //3
                    "bill_code"                 => $item['4'],
                    "bill_date"                 => $item['5'],
                    "bill_prices"               => $package_price,
                    "bill_prices_vat"           => $package_price_vat,
                    "partner_id"                => $partner_id, // 7
                    "buildings_id"              => $building_id, // 8
                    "customer_id"               => $customer_id, // 9
                    "customer_code"             => $item['9'], // 10. Mã KH riêng trong bảng hợp đồng
                    "type_cooperate"            => $item['10'], // 11. Loại hình hợp tác
                    "shop_code"                  => $item['11'], // 12. mã cửa hàng
                    "employee_id"               => $employee_id, // 13. mã nhân viên
                    "status"                    => $item['13'] ?? 1, // trạng thái hợp đồng: 1- chờ ký, 2: đang trong t/g hợp đồng, 3: hợp đồng đã hết hạn
                    "is_deleted"                => 0,
                    "created_at"                => date('Y-m-d H:i:s'),
                    "created_by"                => Auth::user()->id,
                    "package_price"             => $package_price,
                    "package_time_used"         => $package_time_used,
                    "package_time_promotion"    => $package_promotion_time,
                    "percent_share"             => $percent_share,
                ];
            }
            // dd($data_arr);
            DB::table('contracts')->insert($data_arr);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Import dữ liệu thành công");
        } catch (\Throwable $th) {
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }
}
