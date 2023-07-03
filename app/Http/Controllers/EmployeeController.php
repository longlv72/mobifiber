<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Http\Requests\CreateSingleEmployeeRequest;
use App\Http\Requests\UpdateSingleEmployeeRequest;
use App\Imports\EmployeeImport;
use App\Models\RoleGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends CoreController
{
    public $data = [];

    public function __construct()
    {
    }

    public function view()
    {
        $this->data['template_import'] = url('templates/TemplateEmployee.xlsx');
        $this->data['list_role_groups'] = getAllRoleGroup();
        return view('employees.list', $this->data);
    }

    public function export(Request $request) {
        $role_group_id = $request->role_group_id;
        $search_value = mb_strtolower($request->search_value);
        
        $data = User::with('role')
                        ->where('is_deleted', 0)
                        ->where(function($query) use($search_value){
                            $query->when($search_value, function ($query) use ($search_value) {
                                $query->where(DB::raw("LOWER(CONCAT(CONCAT(lastname, ' '), firstname))"), 'LIKE', "%" . $search_value . "%")
                                        ->orWhere(DB::raw("LOWER(address)"), 'LIKE', "%" . $search_value . "%")
                                        ->orWhere(DB::raw("LOWER(email)"), 'LIKE', "%" . $search_value . "%")
                                        ->orWhere(DB::raw("LOWER(phone)"), 'LIKE', "%" . $search_value . "%");

                            });
                        })
                        ->when($role_group_id, function ($query) use ($role_group_id) {
                            $query->where('role_group_id', $role_group_id);
                        })
                        ->orderBy('id', 'desc')
                        ->get();
                            
        $params = ['data' => $data, 'template' => 'exports.employees'];
        $filename = 'employees_at_'.date('H_i_s_d_m_Y').'.xlsx';
        
        return Excel::download(new EmployeeExport($params), $filename);
    }

    public function view_list(Request $request)
    {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $role_group_id = $request->role_group_id;
        $search_value = mb_strtolower($request->search_value);

        $data = User::with('role')
                ->where('is_deleted', 0)
                ->where(function($query) use($search_value){
                    $query->when($search_value, function ($query) use ($search_value) {
                        $query->where(DB::raw("LOWER(CONCAT(CONCAT(lastname, ' '), firstname))"), 'LIKE', "%" . $search_value . "%")
                                ->orWhere(DB::raw("LOWER(address)"), 'LIKE', "%" . $search_value . "%")
                                ->orWhere(DB::raw("LOWER(email)"), 'LIKE', "%" . $search_value . "%")
                                ->orWhere(DB::raw("LOWER(phone)"), 'LIKE', "%" . $search_value . "%");
    
                    });
                })
                ->when($role_group_id, function ($query) use ($role_group_id) {
                    $query->where('role_group_id', $role_group_id);
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

    public function create(CreateSingleEmployeeRequest $request)
    {
        if (!preg_match("/^[a-zA-Z0-9]+$/", $request->username) == 1) {
            return $this->responseJSONFalse(false, "Tài khoản chỉ chứa ký tự a-z A-Z 0-9");
        }
        $data_employee = [
            'firstname'         => $request->first_name,
            'lastname'          => $request->last_name,
            'username'          => $request->username,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'real_manage_unit'  => $request->real_manage_unit,
            'password'          => Hash::make($request->password),
            'is_active'         => $request->is_active,
            'role_group_id'     => $request->role_group_id,
        ];

        DB::beginTransaction();
        try {
            DB::table('users')->insert($data_employee);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Thêm mới thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function update(UpdateSingleEmployeeRequest $request)
    {
        $employee_id = $request->id;
        $data_employee = [
            'firstname'         => $request->first_name,
            'lastname'          => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'real_manage_unit'  => $request->real_manage_unit,
            'is_active'         => $request->is_active,
            'role_group_id'     => $request->role_group_id,
        ];

        DB::beginTransaction();
        try {
            DB::table('users')->where('id', $employee_id)->update($data_employee);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Cập nhật thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server");
        }
    }

    public function delete(Request $request)
    {
        $table_name = 'users';
        $id = $request->id;
        $device = User::where('id', $id)->first();

        if (!$device) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin nhân viên không tồn tại");
        }

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_deleted'   => 1]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Xóa thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function update_status(Request $request)
    {
        $table_name = 'users';
        $id = $request->id;
        $device = User::where('id', $id)->first();

        if (!$device) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin nhân viên không tồn tại");
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

    public function import(Request $request)
    {
        try
        {
            $import = new EmployeeImport();
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

    public function update_data(Request $request) {
        $data = json_decode($request->data, true);
        // return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Không có dữ liệu để import", $data);
        if (!$data || count($data) <= 0) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Không có dữ liệu để import");
        }
        $data_arr = [];
        foreach ($data as $item) {
            $data_arr[] = [
                'firstname'           => $item['0'],
                'lastname'            => $item['1'],
                'username'            => $item['2'],
                'email'               => $item['3'],
                'phone'               => $item['4'],
                'address'             => $item['5'],
                'password'            => Hash::make($item['6']),
                'is_active'           => 1,
                "is_deleted"          => 0,
                'role_group_id'       => $item['7'],
                'created_date'        => date('Y-m-d H:s:i')
            ];
        }

        DB::beginTransaction();
        try {
            DB::table('users')->insert($data_arr);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Import dữ liệu thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function saveFileEmployeeData(Request $request)
    {
    }
}
