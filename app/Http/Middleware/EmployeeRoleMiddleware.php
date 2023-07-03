<?php

namespace App\Http\Middleware;

use App\Models\RoleGroup;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeRoleMiddleware
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
        $user_info = Auth::user();
        $is_employee = $user_info->role_group_id == RoleGroup::EMPLOYEE;
        if ( $is_employee ) {
            
        }
        return $next($request);
    }
}
