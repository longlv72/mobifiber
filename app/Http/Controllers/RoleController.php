<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends CoreController
{
    public $data = [];
    public function __construct()
    {
    }

    public function view(Request $request)
    {
        $listRoleGroup = DB::table('role_groups')->get();
        $this->data['list_role_group'] = $listRoleGroup;
        $this->data['role_id'] = $request->role_id;
        return view('roles.list', $this->data);
    }
    public function view_role(Request $request)
    {
        $id = $request->role_id;
        $listRole = DB::select(
            DB::raw("select r.*, m.module_name, m.module_display from roles r
                    join modules m on r.module_id = m.id
                    where r.role_group_id = :role_id"),
            ['role_id' => $id]
        );
        return response()->json([
            'rows' => $listRole,
            'total' => count($listRole)
        ], 200);
    }
    public function update_role(Request $request)
    {
        $id = $request->id;
        $action = $request->action;
        $roleDataCheck = Role::where('id', $id)->first();
        if ($roleDataCheck) {
            DB::beginTransaction();
            $roleData = Role::where('id', $id);
            switch (strtolower($action)) {
                case 'role_add':
                    $roleData->update(['role_add' => $roleDataCheck->role_add == 1 ? 2 : 1]);
                    break;
                case 'role_edit':
                    $roleData->update(['role_edit' => $roleDataCheck->role_edit == 1 ? 2 : 1]);
                    break;
                case 'role_delete':
                    $roleData->update(['role_delete' => $roleDataCheck->role_delete == 1 ? 2 : 1]);
                    break;
                case 'role_view':
                    $roleData->update(['role_view' => $roleDataCheck->role_view == 1 ? 2 : 1]);
                    break;
                case 'role_import':
                    $roleData->update(['role_import' => $roleDataCheck->role_import == 1 ? 2 : 1]);
                    break;
                case 'role_export':
                    $roleData->update(['role_export' => $roleDataCheck->role_export == 1 ? 2 : 1]);
                    break;
                case 'role_report':
                    $roleData->update(['role_report' => $roleDataCheck->role_report == 1 ? 2 : 1]);
                    break;
                default:
                    break;
            }
            return $this->responseJSONSuccess(true, "Cập nhật quyền thành công");
        }
        return $this->responseJSONFalse(false, "Không tìm thấy quyền hợp lệ");
    }
}
