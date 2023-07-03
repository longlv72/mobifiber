<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEquipmentInstallationJob;
use App\Http\Requests\CreateSingleJobRequest;
use App\Http\Requests\UpdateSingleJobRequest;
use App\Mail\SendingMailAssignJob;
use App\Models\Contract;
use App\Models\CustomerAddress;
use App\Models\Job;
use App\Models\JobEmployee;
use App\Models\JobProcess;
use App\Models\RoleGroup;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class JobController extends CoreController
{
    public $data = [];

    public function __construct()
    {
    }

    public function view(Request $request)
    {
        // dd(DB::table('queue_mail')->where('is_send', 2)->get());
        // $this->data['getAllBuildings'] = getAllBuildings();
        $this->data['getAllCustomers'] = getAllCustomers();
        $this->data['getAllEmployees'] = getEngineerEmployees();
        $this->data['jobTypeList'] = getJobType();
        $this->data['allPartners'] = getAllPartners();
        $this->data['listContracts'] = getContractActive();

        return view('jobs.list', $this->data);
    }

    public function create(CreateSingleJobRequest $request)
    {   
        $employee_ids_array = array_map('intval', explode(',', $request->employee_ids));
        $status = Job::NOT_ASSIGN_YET;
        if ($employee_ids_array && count($employee_ids_array) > 0) {
            $status = Job::ASSIGNED;
        }
        DB::beginTransaction();
        try {
            $created_by_id = Auth::user()->id;
            $created_by_name = Auth::user()->lastname . ' ' . Auth::user()->firstname . ' (' . Auth::user()->username . ') ';
            $created_at = date('Y-m-d H:i:s');
            $job_type = $request->job_type;
            $data_jobs = [];

            // handle insert contract
            if ($job_type == Job::SURVEY) {
                $data_contracts = [
                    'partner_id'        => $request->partner_id,
                    'buildings_id'      => $request->building_id,
                    'address_id'        => $request->address_id,
                    'customer_id'       => $request->customer_id,
                    'code'              => $request->job_code,
                    'status'            => Contract::WAITING_SIGN,
                    'employee_id'       => Auth::user()->id // x viên tạo công việc, coi là ng tạo hợp đồng này
                ];

                $contract_id = DB::table('contracts')->insertGetId($data_contracts);

                $data_jobs = array_merge($data_jobs, ['contract_id' => $contract_id]);
            }

            // handle insert job
            $data_jobs = array_merge(
                $data_jobs,
                [
                    "job_code"              => $request->job_code,
                    "job_type"              => $request->job_type,
                    "building_id"           => $request->building_id,
                    "address_id"            => $request->address_id,
                    "partner_id"            => $request->partner_id,
                    "customer_id"           => $request->customer_id,
                    "descriptions"          => $request->description,
                    "reason"                => $request->reason,
                    'status'                => $status,
                    "created_at"            => $created_at,
                    "created_by"            => $created_by_id
                ]
            );
            $job_contract_id = $request->contract_id;
            if ( $job_contract_id ) {
                $data_jobs = array_merge(
                    $data_jobs,
                    [
                        'contract_id'   => $job_contract_id
                    ]
                );
            }

            $job_id = DB::table('jobs')->insertGetId($data_jobs);
            
            $job_process_data = [];
            
            //handle job_process
            $job_process_data[] = [
                'job_id'        => $job_id,
                'created_by'    => $created_by_id,
                'created_at'    => $created_at,
                'action'        => $created_by_name . 'đã tạo mới'
            ];

            $data_job_employees = [];

            $job_info = Job::with('building', 'customer')->where('id', $job_id)->first();
            $address_or_building = '';
            if ( $request->address_id ) {
                $address_info = CustomerAddress::where('id', $request->address_id)->first();
                $address_or_building = ' địa chỉ ' . $address_info->address;
            } else {
                $address_or_building = ' tòa nhà ' . $job_info->building->building_name . ' - ' . $job_info->building->address;
            }
            $content_job = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', "Ban da duoc giao viec moi: " . $job_info->job_type . ', tai' . $address_or_building);
            // dd($content_job);
            $data_sending_sms = [];

            $name_engineer_employee = [];
            $data_send_mail = [];
            $list_engineer_send_mail = DB::select(DB::raw('select * FROM users WHERE id in (select * from split_string(:list_id))'), ['list_id' => $request->employee_ids]);
            // dd($list_engineer_send_mail);
            foreach (collect($list_engineer_send_mail) as $item) {
                $user_email = $item->email;
                $user_phone = $item->phone;
                $data_job_employees[] = [
                    'job_id'    => $job_id,
                    'user_id'   => $item->id,
                    'status'    => JobEmployee::WORKED
                ];

                // push name to name_array
                array_push($name_engineer_employee,  $item->firstname . ' ' . $item->firstname . ' (' . $item->username . ')');

                if ( $user_email ) {
                    $html =  view('sending_email.assign_job', ['job'    => $job_info])->render();
                    $content = htmlspecialchars($html);
                    $data_send_mail[] = [
                        'content'   => $content,
                        'is_send'   => 2,
                        'send_at'   => null,
                        'email'     => $user_email,
                        'title'     => "GIAO VIỆC MỚI"
                    ];
                    
                };
                $data_sending_sms[] = [
                    'phone'     => $user_phone,
                    'is_send'   => 1,
                    'content'   => $content_job,
                    'send_at'   => date('Y-m-d H:i:s')
                ];
            }
            // dd($data_job_employees);
            DB::table('sms_queue')->insert($data_sending_sms);
            DB::table('job_employees')->insert($data_job_employees);

            if ( count($data_send_mail) > 0 ) {
                DB::table('queue_mail')->insert($data_send_mail);
            }

            if (count($name_engineer_employee) > 0) {
                $job_process_data[] = [
                    'job_id'        => $job_id,
                    'created_by'    => $created_by_id,
                    'created_at'    => $created_at,
                    'action'        => $created_by_name . 'đã thêm ' . implode(', ', $name_engineer_employee) . ' vào công việc'
                ];
            }
            DB::table('job_proccess')->insert($job_process_data);
            return $this->responseJSONSuccessWithData($this::IS_COMMIT_DB, "Thêm mới thành công", ['data_job' => $data_jobs]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }

    public function update(UpdateSingleJobRequest $request)
    {
        $job_id = $request->id;
        $job_info = Job::with('building', 'customer', 'createdBy')->where('id', $job_id)->where('is_deleted', "0")->first();
        if ( ! $job_info) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Công việc không tồn tại");
        }

        // $user_ids_array = $request->employee_ids;
        $employee_ids_array = array_map('intval', explode(',', $request->employee_ids));
        $data_job = [
            "building_id"           => $request->building_id,
            "address_id"            => $request->address_id,
            "partner_id"            => $request->partner_id,
            "descriptions"          => $request->description,
            'status'                => $request->job_status,
            "updated_by"            => Auth::user()->id,
            "updated_at"            => date('Y-m-d H:i:s'),
        ];

        DB::beginTransaction();
        try {
            $check_job_info = Job::with('building', 'customer')->where('id', $job_id)->first();
            $data_job_employees = [];

            $address_or_building = '';
            if ( $request->address_id ) {
                $address_info = CustomerAddress::where('id', $request->address_id)->first();
                $address_or_building = ' địa chỉ ' . $address_info->address;
            } else {
                $address_or_building = ' tòa nhà ' . $job_info->building->building_name . ' - ' . $job_info->building->address;
            }
            $content_job = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', "Ban da duoc giao viec moi: " . $job_info->job_type . ', tai' . $address_or_building);

            $job_process_data = [];
            $name_engineer_employee = [];
            $data_send_mail = [];

            if (count($employee_ids_array) > 0) {
                foreach ($employee_ids_array as $user_id) {
                    $check_item_info = DB::table('job_employees')->where('job_id', $job_id)->where('user_id', $user_id)->first();
                    $user_email = null;
                    if (!$check_item_info) {
                        $user_info = DB::table('users')->where('id', $user_id)->first();
                        if (!$user_info) continue;
                        $user_email = $user_info->email;
    
                        // push name to name_array
                        array_push($name_engineer_employee,  $user_info->firstname . ' ' . $user_info->firstname . ' (' . $user_info->username . ')');
    
                        $data_job_employees[] = [
                            'user_id'   => $user_id,
                            'job_id'    => $job_id,
                            'status'    => JobEmployee::WORKED
                        ];
    
                        $data_sending_sms[] = [
                            'phone'     => $user_info->phone,
                            'is_send'   => 1,
                            'content'   => $content_job,
                            'send_at'   => date('Y-m-d H:i:s')
                        ];
    
                        if ( $user_email ) {
                            $html =  view('sending_email.assign_job', ['job'    => $job_info])->render();
                            $content = htmlspecialchars($html);
                            $data_send_mail[] = [
                                'content'   => $content,
                                'is_send'   => 2,
                                'send_at'   => null,
                                'email'     => $user_email,
                                'title'     => "GIAO VIỆC MỚI"
                            ];
                            // $sending_mail = new SendingMailAssignJob($job_info);
                            // Mail::to($user_email)->send($sending_mail);
                        };
                        if (count($name_engineer_employee)) {
                            $created_by_name = Auth::user()->lastname . ' ' . Auth::user()->firstname . ' (' . Auth::user()->username . ') ';
                            $job_process_data = [
                                'job_id'        => $job_id,
                                'created_by'    => Auth::user()->id,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'action'        => $created_by_name . 'đã thêm ' . implode(', ', $name_engineer_employee) . ' vào công việc'
                            ];
                        }
                    }
                }
            }

            if (count($data_job_employees) > 0) {
                DB::table('job_employees')->insert($data_job_employees);
                DB::table('sms_queue')->insert($data_sending_sms);
            }

            if ( count($job_process_data) > 0 )
            {
                DB::table('job_proccess')->insert($job_process_data);
            }
            if ( count($data_send_mail) > 0 ) {
                DB::table('queue_mail')->insert($data_send_mail);
            }

            $check_number_engineer_in_job = DB::table('job_employees')->where('job_id', $job_id)->get()->count();
            if ($check_number_engineer_in_job > 0 && $check_job_info->status == Job::NOT_ASSIGN_YET) {
                $status = Job::ASSIGNED;
                $data_job = array_merge($data_job, ['status'    => $status]);
            }
            DB::table('jobs')->where('id', $job_id)->update($data_job);

            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Cập nhật thành công");
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage() . ' - line: ' . $th->getLine());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server");
        }
    }

    public function delete(Request $request)
    {
        $table_name = 'jobs';
        $id = $request->id;
        $device = Job::where('id', $id)->first();

        if (!$device) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin công việc không tồn tại");
        }

        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['is_deleted'   => 1]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Xóa thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function update_status(Request $request)
    {
        $table_name = 'jobs';
        $id = $request->id;
        $device = Job::where('id', $id)->first();

        if (!$device) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thông tin công việc không tồn tại");
        }
        $is_active = $request->is_active == 1 ? 2 : 1;
        // dd($is_active);
        DB::beginTransaction();
        try {
            DB::table($table_name)->where('id', $id)->update(['status'   => $is_active]);
            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Đổi trạng thái thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function view_list(Request $request)
    {
        $limit = $request->limit != null ? $request->limit : 10; // page_size
        $offset = $request->offset != null ? $request->offset : 0;
        $status = (int) $request->status;
        // dd($status);
        $search_value = mb_strtolower(str_replace(" ", "", $request->search_value),'UTF-8');

        $data = Job::with('assigned_employee_groups', 'building', 'customer', 'created_by', 'job_type', 'reason_job', 'address')
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search_value, function ($query) use ($search_value) {
                $query->where(function($q) use($search_value){
                    $q->where(DB::raw('lower(job_code)'), 'LIKE', '%' . $search_value . '%')
                        ->orWhere(DB::raw('lower(job_code)'), 'LIKE', '%' . $search_value . '%')
                        ->orWhereHas('created_by', function($q) use($search_value) {
                            $q->where(DB::raw("LOWER(REPLACE(CONCAT(CONCAT(lastname, ' '), firstname), ' ', ''))"), 'LIKE', "%" . $search_value . "%");
                        })
                        ->orWhereHas('building', function($q) use($search_value) {
                            $q->where(DB::raw("LOWER(building_name)"), 'LIKE', "%" . $search_value . "%")
                            ->orWhere(DB::raw("LOWER(REPLACE(CONCAT(CONCAT(building_name, '-'), address), ' ', ''))"), 'LIKE', "%" . $search_value . "%");
                        })
                        ->orWhereHas('customer', function($q) use($search_value) {
                            $q->where(DB::raw("LOWER(CONCAT(CONCAT(last_name, ' '), firstname))"), 'LIKE', "%" . $search_value . "%")
                                ->orWhere(DB::raw("LOWER(phone)"), 'LIKE', "%" . $search_value . "%");
                        })
                        ->orWhereHas('reason_job', function($q) use($search_value) {
                            $q->where(DB::raw("LOWER(REPLACE(value_setting, ' ', ''))"), 'LIKE', "%" . $search_value . "%");
                        })
                        ->orWhereHas('job_type', function($q) use($search_value) {
                            $q->where(DB::raw("LOWER(REPLACE(value_setting, ' ', ''))"), 'LIKE', "%" . $search_value . "%");
                        });
                });
            })
            ->where('is_deleted', 0)
            ->orderBy('id', 'desc');

        $count = $data->count();
        // $data_service = $data->offset($offset)->limit($limit)->get();
        $data_jobs = $data->get();
        return response()->json([
            "success"   => true,
            "message"   => "Lấy dữ liệu thành công",
            'rows'      => $data_jobs,
            'total'     => $count,
        ], 200);
    }

    public function import(Request $request)
    {
    }

    public function update_data(Request $request)
    {
    }


    public function getDataListJobOfEngineer(Request $request) {
        $role_group_id = Auth::user()->role_group_id;
        $this->data['listJobOfEmployee'] = Job::with('assigned_employee_groups', 'job_type', 'reason_job', 'created_by', 'building', 'address')
                                                // ->when($role_group_id == RoleGroup::ADMIN, function ($q) {
                                                    
                                                // })
                                                ->whereHas('assigned_employee_groups', function($query) {
                                                    $query->where('user_id', Auth::user()->id)->orWhere('user_id', Auth::user()->role_group_id == RoleGroup::ADMIN);
                                                })
                                                ->where('status', Job::ASSIGNED)
                                                ->where('is_deleted', "0")
                                                ->orderByDesc('id')
                                                ->get();
        $this->data['processing_job_number'] = Job::with('assigned_employee_groups', 'created_by', 'building')
                                                    // ->when($role_group_id == RoleGroup::ADMIN, function ($q) {
                                                                                                
                                                    // })
                                                    ->whereHas('assigned_employee_groups', function($query) {
                                                        $query->where('user_id', Auth::user()->id);
                                                    })
                                                    ->where('status', Job::PROCESSING)
                                                    ->where('is_deleted', "0")
                                                    ->get();
        $this->data['completed_job_number'] = Job::with('assigned_employee_groups', 'created_by', 'building')
                                                // ->when($role_group_id == RoleGroup::ADMIN, function ($q) {
                                                                                                                                        
                                                // })
                                                ->whereHas('assigned_employee_groups', function($query) {
                                                    $query->where('user_id', Auth::user()->id);
                                                })
                                                ->where('status', Job::COMPLETED)
                                                ->where('is_deleted', "0")
                                                ->get();
        return $this->responseJSONSuccess($this::IS_COMMIT_DB, "ok", $this->data);
    }

    public function getProcessingJobsList(Request $request) {
        $role_group_id = Auth::user()->role_group_id;
        $this->data['getProcessingJobsList'] = Job::with('assigned_employee_groups', 'reason_job', 'job_type', 'created_by', 'building', 'address')
                                                ->whereHas('assigned_employee_groups', function($query) {
                                                    $query->where('user_id', Auth::user()->id);
                                                })
                                                ->where('status', Job::PROCESSING)
                                                ->where('is_deleted', "0")
                                                ->get();
        // $this->data['getProcessingJobsList'] = Job::whereHas('assigned_employee_groups', function($q) {
        //         $q->where('user_id', Auth::user()->id);
        // })->where('status', Job::PROCESSING)->get();

        $this->data['completed_job_number'] = JobProcess::whereHas('job', function ($q) {
            $q->whereHas('assigned_employee_groups', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })->where('status', JobProcess::COMPLETED);
        });
        return $this->responseJSONSuccess($this::IS_COMMIT_DB, "ok", $this->data);
    }

    public function getCompletedJobsList(Request $request) {
        $status = $request->status;   
        // dd($status);     
        $this->data['getCompletedJobsList'] = Job::with('assigned_employee_groups', 'reason_job', 'created_by', 'building', 'address')
                                                ->where(function($q) {
                                                    $q->whereHas('assigned_employee_groups', function($query) {
                                                        $query->where('user_id', Auth::user()->id);
                                                    })->orWhereHas('reject_or_gave_up_employees', function($q) {
                                                        $q->where('user_id', Auth::user()->id);
                                                    });
                                                })
                                                ->when($status && $status != Job::COMPLETED, function($q) use($status){
                                                    $q->where('status', '!=', Job::COMPLETED);
                                                })
                                                ->when($status && $status == Job::COMPLETED, function($q) use($status){
                                                    $q->where('status', Job::COMPLETED);
                                                })
                                                ->where('is_deleted', "0")
                                                ->orderByDesc('id')
                                                ->get();
        return $this->responseJSONSuccess($this::IS_COMMIT_DB, "ok", $this->data);
    }



    // listJobEngineer
    public function listJobEngineer(Request $request)
    {
        return view('jobs_engineer.list_job_engineer');
    }

    public function getUnassignEmployeeByJob(Request $request)
    {
        $job_id = $request->job_id;
        $list_engineer_employee_id = DB::table('job_employees')->where('job_id', $job_id)->pluck('user_id');
        $this->data['listEmployee'] = User::where('role_group_id', RoleGroup::EMPLOYEE)->whereNotIn('id', $list_engineer_employee_id)->where('is_deleted', 0)->get();
        return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "237", $this->data);
    }

    public function view_getEmployeeData(Request $request)
    {
        $employee_array_id = $request->array_employee_id;
        $this->data['engineer_employee_data'] = DB::table('users')->whereIn('id', $employee_array_id)->get();
        return $this->responseJSONSuccessWithData($this::IS_NOT_COMMIT_DB, 'OK', $this->data);
    }

    public function view_ReasonJobList(Request $request) {
        $job_type = $request->job_type;
        if ( ! $job_type ) return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thiếu loại công việc");

        try {
            $job_reason_list = Setting::where('obj_id', $job_type)->get();
            return $this->responseJSONSuccessWithData($this::IS_NOT_COMMIT_DB, "OK", ['job_reason_list' => $job_reason_list]);
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_NOT_ROLL_BACK_DB, "Lỗi server");
        }
    }

    public function create_forward_equipment_installation0(CreateEquipmentInstallationJob $request) {
        $employee_ids_array = array_map('intval', explode(',', $request->employee_ids));
        $status = Job::NOT_ASSIGN_YET;
        if ($employee_ids_array && count($employee_ids_array) > 0) {
            $status = Job::ASSIGNED;
        }
        DB::beginTransaction();
        try {
            $created_by_id = Auth::user()->id;
            $created_by_name = Auth::user()->lastname . ' ' . Auth::user()->firstname . ' (' . Auth::user()->username . ') ';
            $created_at = date('Y-m-d H:i:s');
            $data_jobs = [];

            $job_info = Job::where('id', $request->job_id)->first();

            // handle insert job
            $data_jobs = array_merge(
                $data_jobs,
                [
                    "job_code"              => $request->job_code,
                    "job_type"              => Job::EQUIPMENT_INSTALLATION,
                    "building_id"           => $job_info->building_id,
                    "address_id"            => $job_info->address_id,
                    "partner_id"            => $job_info->partner_id,
                    "customer_id"           => $job_info->customer_id,
                    "descriptions"          => $request->description,
                    "contract_id"           => $request->contract_id,
                    "reason"                => $request->reason,
                    'status'                => $status,
                    "created_at"            => $created_at,
                    "created_by"            => $created_by_id
                ]
            );
            // dd($data_jobs);

            $job_id = DB::table('jobs')->insertGetId($data_jobs);
            
            $job_process_data = [];
            
            //handle job_process
            $job_process_data[] = [
                'job_id'        => $job_id,
                'created_by'    => $created_by_id,
                'created_at'    => $created_at,
                'action'        => $created_by_name . 'đã tạo mới'
            ];

            $data_job_employees = [];

            $job_info = Job::with('building', 'customer', 'address')->where('id', $job_id)->first();
            $address_or_building = '';
            if ( $job_info->address ) {
                $address_or_building = ' địa chỉ ' . $job_info->address->address;
            } else {
                $address_or_building = ' tòa nhà ' . $job_info->building->building_name . ' - ' . $job_info->building->address;
            }
            // dd($address_or_building);
            $content_job = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', "Ban da duoc giao viec moi: " . $job_info->job_type . ', tai' . $address_or_building);
            // dd($content_job);
            $data_sending_sms = [];

            $name_engineer_employee = [];
            $data_send_mail = [];
            $list_engineer_send_mail = DB::select(DB::raw('select * FROM users WHERE id in (select * from split_string(:list_id))'), ['list_id' => $request->employee_ids]);
            // dd($list_engineer_send_mail);
            foreach (collect($list_engineer_send_mail) as $item) {
                $user_email = $item->email;
                $user_phone = $item->phone;
                $data_job_employees[] = [
                    'job_id'    => $job_id,
                    'user_id'   => $item->id,
                    'status'    => JobEmployee::WORKED
                ];

                // push name to name_array
                array_push($name_engineer_employee,  $item->firstname . ' ' . $item->firstname . ' (' . $item->username . ')');

                if ( $user_email ) {
                    $html =  view('sending_email.assign_job', ['job'    => $job_info])->render();
                    $content = htmlspecialchars($html);
                    $data_send_mail[] = [
                        'content'   => $content,
                        'is_send'   => 2,
                        'send_at'   => null,
                        'email'     => $user_email,
                        'title'     => "GIAO VIỆC MỚI"
                    ];
                    
                };
                $data_sending_sms[] = [
                    'phone'     => $user_phone,
                    'is_send'   => 1,
                    'content'   => $content_job,
                    'send_at'   => date('Y-m-d H:i:s')
                ];
            }
            // dd($data_job_employees);
            DB::table('sms_queue')->insert($data_sending_sms);
            DB::table('job_employees')->insert($data_job_employees);

            if ( count($data_send_mail) > 0 ) {
                DB::table('queue_mail')->insert($data_send_mail);
            }

            if (count($name_engineer_employee) > 0) {
                $job_process_data[] = [
                    'job_id'        => $job_id,
                    'created_by'    => $created_by_id,
                    'created_at'    => $created_at,
                    'action'        => $created_by_name . 'đã thêm ' . implode(', ', $name_engineer_employee) . ' vào công việc'
                ];
            }
            DB::table('job_proccess')->insert($job_process_data);
            return $this->responseJSONSuccessWithData($this::IS_COMMIT_DB, "Thêm mới thành công", ['data_job' => $data_jobs]);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
        }
    }
}
