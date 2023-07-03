<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerAddressController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobProcessController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PackageDetailController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoleGroupController;
use App\Http\Controllers\UserController;
use App\Models\Partner;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth', 'is_engineer_employee']], function () {
    Route::get('/', function () {
        return view('layouts.app');
    });

    Route::post('save-profile', [UserController::class, 'saveProfile'])->name('save-profile');
    Route::post('change-pass', [UserController::class, 'changePass'])->name('change-pass');

    // partner route
    Route::get('list-partner', [PartnerController::class, 'view'])->name('list-partner');
    Route::post('create-partner', [PartnerController::class, 'create'])->name('create-partner');
    Route::post('update-partner', [PartnerController::class, 'update'])->name('update-partner');
    Route::delete('delete-partner', [PartnerController::class, 'delete'])->name('delete-partner');
    Route::get('get-list-data-partner', [PartnerController::class, 'view_list'])->name('get-list-data-partner');
    Route::post('import-excel-partner', [PartnerController::class, 'import'])->name('import-excel-partner');
    Route::post('save-file-partner-data', [PartnerController::class, 'update_data'])->name('save-file-partner-data');
    Route::put('deactive-partner', [PartnerController::class, 'update_status'])->name('deactive-partner');
    Route::get('export-partners', [PartnerController::class, 'export'])->name('export-partners');

    // employee
    Route::get('list-employee', [EmployeeController::class, 'view'])->name('list-employee');
    Route::post('create-employee', [EmployeeController::class, 'create'])->name('create-employee');
    Route::post('update-employee', [EmployeeController::class, 'update'])->name('update-employee');
    Route::delete('delete-employee', [EmployeeController::class, 'delete'])->name('delete-employee');
    Route::put('deactive-employee', [EmployeeController::class, 'update_status'])->name('deactive-employee');
    Route::get('get-list-data-employee', [EmployeeController::class, 'view_list'])->name('get-list-data-employee');
    Route::post('import-excel-employee', [EmployeeController::class, 'import'])->name('import-excel-employee');
    Route::post('save-file-employee-data', [EmployeeController::class, 'update_data'])->name('save-file-employee-data');
    Route::get('export-employees', [EmployeeController::class, 'export'])->name('export-employees');


    // package
    Route::get('list-package', [PackageController::class, 'view'])->name('list-package');
    Route::post('create-package', [PackageController::class, 'create'])->name('create-package');
    Route::post('update-package', [PackageController::class, 'update'])->name('update-package');
    Route::delete('delete-package', [PackageController::class, 'delete'])->name('delete-package');
    Route::put('deactive-package', [PackageController::class, 'update_status'])->name('deactive-package');
    Route::get('get-list-data-package', [PackageController::class, 'view_list'])->name('get-list-data-package');
    Route::post('import-excel-package', [PackageController::class, 'import'])->name('import-excel-package');
    Route::post('save-file-package-data', [PackageController::class, 'update_data'])->name('save-file-package-data');
    Route::get('export-packages', [PackageController::class, 'export'])->name('export-packages');

    Route::post('save-package-detail', [PackageDetailController::class, 'create'])->name('save-package-detail'); // add or edit package detail
    Route::get('get-list-package-detail', [PackageDetailController::class, 'view_listPackageDetail'])->name('get-list-package-detail');
    Route::delete('delete-package-detail', [PackageDetailController::class, 'delete'])->name('delete-package-detail');

    // contracts
    Route::get('list-contracts', [ContractController::class, 'view_index'])->name('list-contracts');
    Route::get('list-building-by-partner', [ContractController::class, 'view_list'])->name('list-building-by-partner');
    Route::post('create-single-contract', [ContractController::class, 'create'])->name('create-single-contract');
    Route::post('update-single-contract', [ContractController::class, 'update'])->name('update-single-contract');
    Route::delete('delete-contract', [ContractController::class, 'delete'])->name('delete-contract');
    Route::put('deactive-contract', [ContractController::class, 'update_data'])->name('deactive-contract');
    Route::get('get-list-data-contracts', [ContractController::class, 'view'])->name('get-list-data-contracts');
    Route::post('import-excel-contracts', [ContractController::class, 'import'])->name('import-excel-contracts');
    Route::post('save-file-contracts-data', [ContractController::class, 'update_excel'])->name('save-file-contracts-data');
    Route::get('export-contracts', [ContractController::class, 'export'])->name('export-contracts');

    // report
    Route::get('report-revenus-month', [ReportController::class, 'reportRevenueMonth'])->name('report-revenus-month');
    Route::get('report-growth', [ReportController::class, 'view_growth'])->name('report-growth');
    Route::get('report-list-growth', [ReportController::class, 'view_reportgrowth'])->name('report-list-growth');
    Route::get('report', [ReportController::class, 'listReport'])->name('report');
    Route::get('report-customer', [ReportController::class, 'listReportCustomer'])->name('report-customer');
    Route::get('report-customer-exist', [ReportController::class, 'reportCustomerExist'])->name('report-customer-exist');
    Route::get('report-customer-exist-all', [ReportController::class, 'reportCustomerExistAll'])->name('report-customer-exist-all');

    // devices
    Route::get('list-devices', [DeviceController::class, 'view'])->name('list-devices');
    Route::post('create-single-device', [DeviceController::class, 'create'])->name('create-single-device');
    Route::post('update-single-device', [DeviceController::class, 'update'])->name('update-single-device');
    Route::delete('delete-device', [DeviceController::class, 'delete'])->name('delete-device');
    Route::put('deactive-device', [DeviceController::class, 'update_status'])->name('deactive-device');
    Route::get('get-list-data-devices', [DeviceController::class, 'view_list'])->name('get-list-data-devices');
    Route::post('import-excel-devices', [DeviceController::class, 'import'])->name('import-excel-devices');
    Route::post('save-file-devices-data', [DeviceController::class, 'update_data'])->name('save-file-devices-data');
    Route::get('export-devices', [DeviceController::class, 'export'])->name('export-devices');

    // buildings
    Route::get('list-buildings', [BuildingController::class, 'view_index'])->name('list-buildings');
    Route::get('view-map', [BuildingController::class, 'view_map'])->name('view-map');
    Route::get('list-buildings-map', [BuildingController::class, 'view_list_map'])->name('list-buildings-map');
    Route::post('create-single-building', [BuildingController::class, 'create'])->name('create-single-building');
    Route::post('update-single-building', [BuildingController::class, 'update'])->name('update-single-building');
    Route::delete('delete-building', [BuildingController::class, 'delete'])->name('delete-building');
    Route::put('deactive-building', [BuildingController::class, 'update_data'])->name('deactive-building');
    Route::get('get-list-data-buildings', [BuildingController::class, 'view'])->name('get-list-data-buildings');
    Route::post('import-excel-buildings', [BuildingController::class, 'import'])->name('import-excel-buildings');
    Route::post('save-file-buildings-data', [BuildingController::class, 'update_excel'])->name('save-file-building-data');
    Route::get('export-buildings', [BuildingController::class, 'export'])->name('export-buildings');

    // customer
    Route::get('list-customers', [CustomerController::class, 'view'])->name('list-customers');
    Route::post('create-single-customer', [CustomerController::class, 'create'])->name('create-single-customer');
    Route::post('update-single-customer', [CustomerController::class, 'update'])->name('update-single-customer');
    Route::delete('delete-customer', [CustomerController::class, 'delete'])->name('delete-customer');
    Route::get('get-list-data-customers', [CustomerController::class, 'view_list'])->name('get-list-data-customers');
    Route::post('import-excel-customers', [CustomerController::class, 'import'])->name('import-excel-customers');
    Route::post('save-file-customers-data', [CustomerController::class, 'update_data'])->name('save-file-customer-data');
    Route::post('save-address', [CustomerAddressController::class, 'update'])->name('save-address');
    Route::get('get-data-address', [CustomerAddressController::class, 'view'])->name('get-data-address');
    Route::delete('delete-address', [CustomerAddressController::class, 'delete'])->name('delete-address');
    Route::get('export-customer', [CustomerController::class, 'export'])->name('export-customer');

    // job
    Route::get('list-jobs', [JobController::class, 'view'])->name('list-jobs');
    Route::post('create-single-job', [JobController::class, 'create'])->name('create-single-job');
    Route::post('update-single-job', [JobController::class, 'update'])->name('update-single-job');
    Route::delete('delete-job', [JobController::class, 'delete'])->name('delete-job');
    Route::put('deactive-job', [JobController::class, 'update_status'])->name('deactive-job');
    Route::get('get-list-data-jobs', [JobController::class, 'view_list'])->name('get-list-data-jobs');
    Route::post('import-excel-jobs', [JobController::class, 'import'])->name('import-excel-jobs');
    Route::post('save-file-jobs-data', [JobController::class, 'update_data'])->name('save-file-customer-data');
    Route::get('get-list-job-processes', [JobProcessController::class, 'getListJobProccesses'])->name('get-list-job-processes');
    Route::get('get-reason-job-list', [JobController::class, 'view_ReasonJobList'])->name('get-reason-job-list');
    Route::post('create-equipment-job', [JobController::class, 'create_forward_equipment_installation0'])->name('create-equipment-job');

    // role
    Route::get('list-roles', [RoleController::class, 'view'])->name('list-roles');
    Route::get('list-role-groups', [RoleGroupController::class, 'view'])->name('list-role-groups');
    Route::get('create-role-groups', [RoleGroupController::class, 'create'])->name('create-role-groups');
    Route::get('update-role-groups', [RoleGroupController::class, 'update'])->name('update-role-groups');
    Route::get('get-list-role-groups', [RoleGroupController::class, 'view_list'])->name('get-list-role-groups');
    Route::get('get-list-roles', [RoleController::class, 'view_role'])->name('list-role-detail');
    Route::get('update-roles', [RoleController::class, 'update_role'])->name('update_role');

    // auth
    Route::get('logout', [AuthController::class, 'logout'])->name('logout')->withoutMiddleware('is_engineer_employee');
    Route::get('denied', [AuthController::class, 'denied'])->name('denied');
    Route::get('test-mail', [CommentController::class, 'testMail'])->name('test-mail');
});

Route::group(['middleware' => ['auth'], 'prefix'  => 'engineer'], function () {
    Route::get('/', [JobController::class, 'listJobEngineer'])->name('engineer');
    Route::get('list-job-of-employee', [JobController::class, 'getDataListJobOfEngineer'])->name('list-job-of-employee');
    Route::post('process-receive-job', [JobProcessController::class, 'processReceiveJob'])->name('process-receive-job');
    Route::get('get-processing-jobs-list', [JobController::class, 'getProcessingJobsList'])->name('get-processing-jobs-list');
    Route::get('get-completed-jobs-list', [JobController::class, 'getCompletedJobsList'])->name('get-completed-jobs-list');
    Route::get('get-unassign-employee-by-job', [JobController::class, 'getUnassignEmployeeByJob'])->name('get-unassign-employee-by-job');
    Route::post('reject-job', [JobProcessController::class, 'rejectJob'])->name('reject-job');
    Route::post('add-engineer-job', [JobProcessController::class, 'addEngineerEmployeeForJob'])->name('add-engineer-job');
    Route::post('out-job', [JobProcessController::class, 'outJob'])->name('out-job');
    Route::post('mark-completed-job', [JobProcessController::class, 'markCompleteJob'])->name('mark-completed-job');
    Route::get('get-engineer-employee-data', [JobController::class, 'view_getEmployeeData'])->name('get-engineer-employee-data');

    Route::post('post-comment', [CommentController::class, 'postComment'])->name('post-comment');
    Route::get('get-comment-data', [CommentController::class, 'getListCommentsData'])->name('get-comment-data');
    Route::delete('delete-comment', [CommentController::class, 'deleteComment'])->name('delete-comment');
    Route::get('get-job-process-data', [JobProcessController::class, 'getJobProcessData'])->name('get-job-process-data');

    Route::post('save-profile-engineer-employee', [UserController::class, 'saveProfile'])->name('save-profile-engineer-employee');
    Route::post('change-pass-engineer-employee', [UserController::class, 'changePass'])->name('change-pass-engineer-employee');
});

Route::get('login', [AuthController::class, 'login'])->name('login');

Route::post('login', [AuthController::class, 'loginAction'])->name('login');
