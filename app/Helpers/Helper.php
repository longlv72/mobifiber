<?php

use App\Models\Building;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Device;
use App\Models\Package;
use App\Models\Partner;
use App\Models\RoleGroup;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function getAllRoleGroup()
{
    $data = RoleGroup::all();
    return $data;
}
function GetReasonJobList($job_type){
    if ( ! $job_type ) return ;

    return Setting::where('obj_id', $job_type)->get();

}

function CountNumberCommentByJobId($job_proccess_id) {
    if (! $job_proccess_id) return 0;
    return DB::table('comments')->where('job_proccess_id', $job_proccess_id)->get()->count();
}

function getAllPackage()
{
    $data = Package::whereHas('package_detail')->where('is_deleted', 0)->get();
    // $data = Package::where('is_active', 1)->where('is_deleted', 0)->get();
    return $data;
}

function getAllBuildings()
{
    $data = Building::where('is_deleted', 0)->get();
    // $data = Building::where('is_active', 1)->where('is_deleted', 0)->get();
    return $data;
}

function getAllCustomers()
{
    $data = Customer::where('is_deleted', 0)->distinct('phone')->get();
    // $data = Customer::where('is_active', 1)->where('is_deleted', 0)->distinct('phone')->get();
    return $data;
}

function getAllDevices()
{
    $data = Device::where('is_deleted', 0)->get();
    // $data = Device::where('is_active', 1)->where('is_deleted', 0)->get();
    return $data;
}

function getAllPartners()
{
    $data = Partner::where('is_deleted', '0')->get();
    return $data;
}

function getAllEmployees()
{
    $data = User::where('is_deleted', 0)->get();
    // $data = User::where('is_active', 1)->where('role_group_id', RoleGroup::EMPLOYEE)->get();
    return $data;
}

function getEngineerEmployees()
{
    $data = User::where('role_group_id', RoleGroup::ENGINEER_EMPLOYEE)->get();
    // $data = User::where('is_active', 1)->where('role_group_id', RoleGroup::EMPLOYEE)->get();
    return $data;
}


function getJobType() {
    $data_job_type = Setting::where('key_setting', 'job_type')->get();
    return $data_job_type;
}

function getContractActive() {
    $data_contracts = Contract::with('customer')->whereHas('customer', function($q) {
        $q->where('is_deleted', 0);
    })->where('status', Contract::ACTIVE)->get();
    return $data_contracts;
}
