<?php

namespace App\Http\Controllers;

use App\Exports\PartnerExport;
use App\Http\Requests\CreatePartnerRequest;
use App\Http\Requests\UpdatePartnerRequest;
use App\Imports\PartnerImport;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class PartnerController extends CoreController
{
    public $data = [];
    public function __construct() {

    }
    public function view(Request $request)
    {
        $this->data['template_partner'] = url('templates/TemplatePartner.xlsx');
        return view('partners.list', $this->data);
    }

    public function export(Request $request) {
        $cooperate = $request->cooperate;
        $status = $request->status;
        $search_value = mb_strtolower($request->search_value);

        $data = Partner::where('is_deleted', 0)
                        ->when($cooperate, function($query) use($cooperate){
                            $query->where('cooperate', $cooperate);
                        })
                        ->when($status, function($query) use($status){
                            $query->where('is_active', $status);
                        })
                        ->where(function($q) use($search_value) {
                            $q->when($search_value, function($query) use($search_value){
                                $query->where(DB::raw('lower(partner_name)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(email)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(phone)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(number_bank)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(contact_name)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(contact_phone)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(bank_name)'), 'like', '%'.$search_value.'%')
                                        ->orWhere(DB::raw('lower(partner_code)'), 'like', '%'.$search_value.'%');
                            });
                        })
                        ->orderBy('id', 'desc')
                        ->get();
                            
        $params = ['data' => $data, 'template' => 'exports.partners'];
        $filename = 'partners_at_'.date('H_i_s_d_m_Y').'.xlsx';
        
        return Excel::download(new PartnerExport($params), $filename);
    }

    public function update_status(Request $request) {
        $table_name = 'partners';
        $id = $request->id;
        $device = Partner::where('id', $id)->first();

        if (!$device) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin thiết bị không tồn tại");
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

    public function view_list(Request $request) {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $cooperate = $request->cooperate;
        $status = $request->status;
        $search_value = mb_strtolower($request->search_value);

        $data = Partner::where('is_deleted', 0)
                            ->when($cooperate, function($query) use($cooperate){
                                $query->where('cooperate', $cooperate);
                            })
                            ->when($status, function($query) use($status){
                                $query->where('is_active', $status);
                            })
                            ->where(function($q) use($search_value) {
                                $q->when($search_value, function($query) use($search_value){
                                    $query->where(DB::raw('lower(partner_name)'), 'like', '%'.$search_value.'%')
                                            ->orWhere(DB::raw('lower(email)'), 'like', '%'.$search_value.'%')
                                            ->orWhere(DB::raw('lower(phone)'), 'like', '%'.$search_value.'%')
                                            ->orWhere(DB::raw('lower(number_bank)'), 'like', '%'.$search_value.'%')
                                            ->orWhere(DB::raw('lower(contact_name)'), 'like', '%'.$search_value.'%')
                                            ->orWhere(DB::raw('lower(contact_phone)'), 'like', '%'.$search_value.'%')
                                            ->orWhere(DB::raw('lower(bank_name)'), 'like', '%'.$search_value.'%')
                                            ->orWhere(DB::raw('lower(partner_code)'), 'like', '%'.$search_value.'%');
                                });
                            })
                            ->orderBy('id', 'desc');
        $count = $data->count();
        // $data_service = $data->offset($offset)->limit($limit)->get();
        $data_customer = $data->get();
        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu khách hàng thành công",
            'rows'      => $data_customer,
            'total'     => $count,
        ], 200);
    }

    public function create(CreatePartnerRequest $request) {
        $data_partner = [
            'partner_name'      => $request->partner_name,
            'email'             => $request->partner_email,
            'phone'             => $request->partner_phone,
            'address'           => $request->partner_address,
            // 'type'              => 0,
            'partner_code'      => $request->partner_code,
            'business_license'  => $request->business_license,
            'number_bank'       => $request->number_bank,
            'bank_name'         => $request->bank_name,
            'contact_phone'     => $request->contact_phone,
            'contact_name'      => $request->contact_name,
            'cooperate'         => $request->cooperate,
            'is_active'         => $request->is_active,
            'created_at'        => date('Y-m-d h:i:s'),
            'created_by'        => Auth::user()->id
        ];

        DB::beginTransaction();
        try {
            DB::table('partners')->insert($data_partner);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Thêm mới đối tác thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function update(UpdatePartnerRequest $request) {
        $partner_id = $request->id;
        $data_partner = [
            'partner_name'      => $request->partner_name,
            'email'             => $request->partner_email,
            'phone'             => $request->partner_phone,
            'address'           => $request->partner_address,
            // 'type'              => 0,
            'partner_code'      => $request->partner_code,
            'business_license'  => $request->business_license,
            'number_bank'       => $request->number_bank,
            'bank_name'         => $request->bank_name,
            'contact_phone'     => $request->contact_phone,
            'contact_name'      => $request->contact_name,
            'cooperate'         => $request->cooperate,
            'is_active'         => $request->is_active,
            'updated_at'        => date('Y-m-d h:i:s'),
            'updated_by'        => Auth::user()->id
        ];

        DB::beginTransaction();
        try {
            DB::table('partners')->where('id', $partner_id)->update($data_partner);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Cập nhật thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server");
        }
    }

    public function delete(Request $request) {
        $table_name = 'partners';
        $id = $request->id;
        $device = Partner::where('id', $id)->first();

        if (!$device) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin đối tác không tồn tại");
        }

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_deleted' => 1]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Xóa thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Xóa thất bại. Lỗi server");
        }

    }

    public function import(Request $request) {
        try {
            $import = new PartnerImport();
            $file = $request->file('partnerExcel');
            Excel::import($import, $file);

            // Lấy mảng các hàng lỗi
            $rows = $import->partner_row_errors;

            // Trả lại mảng các hàng lỗi cho client
            // return response()->json(['errors' => $errors]);
            return $this->responseJSONSuccess($this::IS_NOT_COMMIT_DB, "190", [
                'rows'          => $rows,
                'total'         => count($rows),
                'totalError'    => $import->countError,
                'row_errors'    => $import->row_errors,
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
        $data_partner_arr = [];
        foreach ($data as $partner_item) {
            $data_partner_arr[] = [
                'partner_name'      => $partner_item['0'],
                'email'             => $partner_item['1'],
                'phone'             => $partner_item['2'],
                'address'           => $partner_item['3'],
                'partner_code'      => $partner_item['4'],
                'business_license'  => $partner_item['5'],
                'number_bank'       => $partner_item['6'],
                'bank_name'         => $partner_item['7'],
                'contact_name'      => $partner_item['8'],
                'contact_phone'     => $partner_item['9'],
                'cooperate'         => $partner_item['10'],
                'is_active'         => 1,
                'is_deleted'        => 0,
                'created_at'        => date('Y-m-d H:i:s'),
            ];
        }

        DB::beginTransaction();
        try {
            DB::table('partners')->insert($data_partner_arr);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Import dữ liệu thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }
}
