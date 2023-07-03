<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RoleGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleGroupController extends CoreController
{
    public function __construct()
    {
    }
    public function view()
    {
        return view('rolegroups.list');
    }
    public function view_list(Request $request){
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;

        $data = RoleGroup::orderBy('id', 'desc');
        $count = $data->count();
        $data_role = $data->get();
        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu thành công",
            'rows'      => $data_role,
            'total'     => $count,
        ], 200);
    }
    public function create(Request $request)
    {
        $data_role = [
            'role_name'      => $request->role_name,
            'updated_at'        => date('Y-m-d h:i:s')
        ];
        DB::beginTransaction();
        try {
            DB::table('role_groups')->insert($data_role);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Thêm mới nhóm quyền thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Thêm mới nhóm quyền không thành công");
        }
    }
    public function update(Request $request)
    {
        $id = $request->id;
        $role_name = $request->role_name;
        $roleCheck = RoleGroup::where('id', $id)->first();
        if($roleCheck){
            $roleData = RoleGroup::where('id', $id);
            $roleData->update(['role_name' => $role_name]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Cập nhật nhóm quyền thành công");
        }
        return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Nhóm quyền không tồn tại");
    }
}
