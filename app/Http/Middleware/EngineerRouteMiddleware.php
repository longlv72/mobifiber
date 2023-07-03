<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\RoleGroup;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EngineerRouteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $data = $request->route();
        // dd($data);
        if (isset($data->action['controller'])) {
            $routeWeb = explode("\\", $data->action['controller'])[3];
            $controller = str_replace("controller", "", strtolower(explode("@", $routeWeb)[0]));
            $action = strtolower(explode("@", $routeWeb)[1]);
            $user = Auth::user();
            $role_id = $user->role_group_id;
            if (!$user) {
                return redirect()->route('login');
            }
            if ($role_id == RoleGroup::ENGINEER_EMPLOYEE) {
                return redirect()->route('engineer');
            }
            $listRole = DB::select(
                DB::raw("select r.* from roles r
                        join modules m on r.module_id = m.id
                        where r.role_group_id = :role_id
                        and lower(m.module_name) = lower(:module_name)"),
                ['role_id' => $role_id, 'module_name' => $controller]
            );

            $dataRole = collect($listRole)->first();

            if ($dataRole) {
                $roleAccept = true;
                if (str_contains($action, "create")) {
                    $roleAccept = $dataRole->role_add == 1 ? false : true;
                } else if (str_contains($action, "update")) {
                    $roleAccept = $dataRole->role_edit == 1 ? false : true;
                } else if (str_contains($action, "delete")) {
                    $roleAccept = $dataRole->role_delete == 1 ? false : true;
                } else if (str_contains($action, "view")) {
                    $roleAccept = $dataRole->role_view == 1 ? false : true;
                } else if (str_contains($action, "import")) {
                    $roleAccept = $dataRole->role_import == 1 ? false : true;
                } else if (str_contains($action, "export")) {
                    $roleAccept = $dataRole->role_export == 1 ? false : true;
                } else if (str_contains($action, "report")) {
                    $roleAccept = $dataRole->role_report == 1 ? false : true;
                } else {
                    $roleAccept = false;
                }
                if (!$roleAccept) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success'   => false,
                            'message'   => "Bạn không có quyền truy cập tính năng này"
                        ], 200);
                    } else {
                        return redirect()->route('denied');
                    }
                }
            }
        }
        return $next($request);
    }
}
