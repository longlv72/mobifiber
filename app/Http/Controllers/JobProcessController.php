<?php

namespace App\Http\Controllers;

use App\Mail\SendingMailAssignJob;
use App\Mail\SendingMailForCreatedJobPerson;
use App\Models\Contract;
use App\Models\CustomerAddress;
use App\Models\Job;
use App\Models\JobEmployee;
use App\Models\JobProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class JobProcessController extends CoreController
{
    public $data = [];

    public function __construct() {

    }

    public function getJobProcessData(Request $request) {
        $job_id = $request->job_id;
        if ( ! $job_id ) return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thiếu ID công việc");

        $job_info = Job::where('id', $job_id)->where('is_deleted', 0)->first();
        if ( ! $job_info ) return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, " Công việc không tồn tại");

        // hanlde get list job_process
        $list_job_processes = JobProcess::with(['comments' => function($q) {
                                                $q->with('created_by');
                                            }])
                                            ->where('job_id', $job_id)
                                            ->orderBy('id')
                                            ->get();

        return $this->responseJSONSuccessWithData($this::IS_NOT_COMMIT_DB, "OK", ['list_job_processes'  => $list_job_processes]);

    }

    public function processReceiveJob(Request $request){
        $this->data['job_id'] = $job_id = $request->job_id;
        $job_info = Job::with('address', 'building', 'customer', 'createdBy', 'reason_job')->where('id', $job_id)->where('is_deleted', "0")->first();
        if ( ! $job_info) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Công việc không tồn tại");
        }
        // dd($job_info->createdBy);

        DB::beginTransaction();
        try {
            $table = 'jobs';
            $data_sending_sms = [];

            $job_process_info = DB::table($table)->where('id', $this->data['job_id'])->where('status', Job::PROCESSING)->first();

            if ($job_process_info) {
                return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Công việc này đã chuyển trạng thái hoặc không tồn tại");
            }
            $data_job_process = [
                'job_id'                => $this->data['job_id'],
                'created_at'            => date('Y-m-d H:i:s'),
                'created_by'            => Auth::user()->id,
                'note'                  => $request->job_note ?? null,
            ];
            $action_note = '';
            $status_note = Auth::user()->lastname . ' ' . Auth::user()->firstname . '(' . Auth::user()->username . ') đã chuyển trạng thái từ chờ nhận sang đang xử lý';
            $name_engineer_employee = [];
            if ($request->additional_assign) {
                $additional_assign = array_map('intval', explode(',', $request->additional_assign));
                $data_send_mail = [];

                // handle add addtional employee
                foreach ($additional_assign as $item) {
                    $check_engineer_employee_assign = DB::table('job_employees')->where('user_id', $item)->where('job_id', $this->data['job_id'])->first();
                    if ($check_engineer_employee_assign) {
                        continue;
                    } else {
                        $engineer_employee_info = DB::table('users')->where('id', $item)->first();
                        if ( ! $engineer_employee_info ) continue ;
                        $user_email = $engineer_employee_info->email;
                        array_push($name_engineer_employee, $engineer_employee_info->firstname . ' (' . $engineer_employee_info->username . ')');
                        DB::table('job_employees')->insert(['user_id' => $item, 'job_id'    => $this->data['job_id']]);

                        $data_sending_sms[] = [
                            'phone'     => $engineer_employee_info->phone,
                            'is_send'   => 1,
                            'content'   => iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', "Ban da duoc giao viec: " . $job_info->reason_job->value_setting . " tai toa nha " . $job_info->building->building_name . " - " . $job_info->building->address),
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
                    }
                }
            }

            
            $action_note = $status_note;
            if (count($name_engineer_employee) > 0) {
                $action_note = $status_note . ', và thêm ' . implode(', ', $name_engineer_employee) . ' vào nhiệm vụ';
            }

            $data_job_process = array_merge($data_job_process, ['action'    => $action_note]);

            //handle save data to sending email
            $data_sending_sms[] = [
                'phone'     => $job_info->createdBy->phone,
                'is_send'   => 1,
                'content'   => iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', Auth::user()->lastname . " " . Auth::user()->firstname . " da tiep nhan viec " . $job_info->reason_job->value_setting ." va them " . implode(', ', $name_engineer_employee) . " vao nhiem vu"),
                'send_at'   => date('Y-m-d H:i:s')
            ];
            
            DB::table('job_proccess')->insert($data_job_process);

            $data_update_job = [
                'status'        => Job::PROCESSING,
            ];
            
            DB::table('jobs')->where('id', $job_id)->update($data_update_job);

            DB::table('sms_queue')->insert($data_sending_sms);

            // handle sending email for created job persion
            // $sending_for_created_job_person_mail = new SendingMailForCreatedJobPerson($job_info, $action_note);
            // Mail::to($job_info->createdBy->email)->send($sending_for_created_job_person_mail);
            $html =  view('sending_email.job_update', ['job'    => $job_info, 'content' => $action_note])->render();
            $content = htmlspecialchars($html);
            $data_send_mail[] = [
                'content'   => $content,
                'is_send'   => 2,
                'send_at'   => null,
                'email'     => $job_info->createdBy->email,
                'title'     => "Công Việc Có Sự Thay Đổi"
            ];
            
            if ( count($data_send_mail) > 0 ) {
                DB::table('queue_mail')->insert($data_send_mail);
            }

            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Đã tiếp nhận công việc", $this->data);
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage() . ' line: ' . $th->getLine());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!. Chuyển trạng thái lỗi");
        }
    }

    public function addEngineerEmployeeForJob(Request $request) {
        $note = $request->note;
        $job_id = $request->job_id;
        $job_info = Job::with('building', 'customer', 'createdBy', 'reason_job', 'address')->where('id', $job_id)->where('is_deleted', "0")->first();
        if ( ! $job_info) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Công việc không tồn tại");
        }

        $user_info = Auth::user();
        $user_name_info = $user_info->lastname . ' ' . $user_info->firstname . '(' . $user_info->username . ')';
        $user_id = $user_info->id;

        DB::beginTransaction();
        try {
            $data_sending_sms = [];

            $data_job_process = [
                'job_id'        => $job_id,
                'created_at'    => date('Y-m-d H:i:s'),
                'created_by'    => $user_id,
                'note'          => $request->reason,
            ];
            $action_note = '';
            $name_engineer_employee = [];
            $data_send_mail = [];
            if ($request->employee_id_array) {
                $employee_id_array = array_map('intval', explode(',', $request->employee_id_array));
                
                // handle add addtional engineer employee
                foreach ($employee_id_array as $item) {
                    $check_engineer_employee_assign = DB::table('job_employees')->where('user_id', $item)->where('job_id', $job_id)->where('status', JobEmployee::WORKED)->first();
                    if ($check_engineer_employee_assign) {
                        continue;
                    } else {
                        $engineer_employee_info = DB::table('users')->where('id', $item)->first();
                        if ( ! $engineer_employee_info ) continue;
                        $user_email = $engineer_employee_info->email;

                        array_push($name_engineer_employee, $engineer_employee_info->firstname . ' (' . $engineer_employee_info->username . ')');
                        DB::table('job_employees')->insert(['user_id' => $item, 'job_id'    => $job_id]);

                        $address_or_building = '';
                        if ( ! $job_info->building ) {
                            $address_or_building = ' địa chỉ ' . $job_info->address->address;
                        } else if ($job_info->building) {
                            $address_or_building = ' tòa nhà ' . $job_info->building->building_name . ' - ' . $job_info->building->address;
                        }
                        $data_sending_sms[] = [
                            'phone'     => $engineer_employee_info->phone,
                            'is_send'   => 1,
                            'content'   => iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', "Ban da duoc giao viec moi: " . $job_info->reason_job->value_setting . " tai " . $address_or_building),
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
                    }
                }
            }

            if (count($name_engineer_employee) > 0) {
                $action_note = $user_name_info . ' thêm ' . implode(', ', $name_engineer_employee) . ' vào nhiệm vụ';
            }

            $data_sending_sms[] = [
                'phone'     => $job_info->createdBy->phone,
                'is_send'   => 1,
                'content'   =>  iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', Auth::user()->lastname . " " . Auth::user()->firstname . " da them " .implode(', ', $name_engineer_employee) . " vao nhiem vu. Ly do: " . $request->reason),
                'send_at'   => date('Y-m-d H:i:s')
            ];

            $data_job_process = array_merge($data_job_process, ['action'    => $action_note]);

            DB::table('job_proccess')->insert($data_job_process);

            // handle sms
            DB::table('sms_queue')->insert($data_sending_sms);
            // end handle sms

            // handle sending email for created job persion
            $html =  view('sending_email.job_update', ['job'    => $job_info, 'content' => $action_note])->render();
            $content = htmlspecialchars($html);
            $data_send_mail[] = [
                'content'   => $content,
                'is_send'   => 2,
                'send_at'   => null,
                'email'     => $job_info->createdBy->email,
                'title'     => "Công Việc Có Sự Thay Đổi"
            ];
            // dd(count($data_send_mail));
            if ( count($data_send_mail) > 0 ) {
                DB::table('queue_mail')->insert($data_send_mail);
            }
            // $sending_for_created_job_person_mail = new SendingMailForCreatedJobPerson($job_info, $action_note);
            // Mail::to($job_info->createdBy->email)->send($sending_for_created_job_person_mail);

            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Bổ sung nhân viên thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage() . ' line: ' . $th->getLine());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function rejectJob(Request $request) {
        $job_id = $request->job_id;
        $job_info = Job::with('address', 'building', 'customer', 'createdBy', 'reason_job')->where('id', $job_id)->where('is_deleted', "0")->first();
        if ( ! $job_info) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Công việc không tồn tại");
        }

        $user_info = Auth::user();
        $user_name_info = $user_info->lastname . ' ' . $user_info->firstname . '(' . $user_info->username . ')';
        $user_id = $user_info->id;

        DB::beginTransaction();
        try {
            $data_job_process = [
                'job_id'        => $job_id,
                'created_at'    => date('Y-m-d H:i:s'),
                'created_by'    => $user_id,
                'note'          => $request->reason_reject,
                'action'        => $user_name_info . ' từ chối nhận việc'
            ];
            DB::table('job_proccess')->insert($data_job_process);
            DB::table('job_employees')->where('user_id', $user_id)->where('job_id', $job_id)->update(['status'  => JobEmployee::REJECTED]);
            $employee_processing_job = DB::table('job_employees')->where('job_id', $job_id)->where('status', JobEmployee::WORKED)->get()->count();
            if ($employee_processing_job <= 0) {
                DB::table('jobs')->where('id', $job_id)->update(['status'   => Job::ASSIGNED_BUT_BE_DENIED]);
            }

            $content_for_mail = Auth::user()->lastname . " " . Auth::user()->firstname . " đã từ chối nhận việc " . $job_info->reason_job->value_setting . ". Lý do: " . $request->reason_reject;
            // handle save data to sending sms
            $content_for_sms = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $content_for_mail);
            $data_sending_sms = [
                'phone'     => $job_info->createdBy->phone,
                'is_send'   => 1,
                'content'   => $content_for_sms,
                'send_at'   => date('Y-m-d H:i:s')
            ];

            DB::table('sms_queue')->insert($data_sending_sms);
            // dd($content_for_mail);
            // handle sending email for created job persion
            $html =  view('sending_email.job_update', ['job'    => $job_info, 'content' => $content_for_mail])->render();
            $content = htmlspecialchars($html);
            $data_send_mail = [
                'content'   => $content,
                'is_send'   => 2,
                'send_at'   => null,
                'email'     => $job_info->createdBy->email,
                'title'     => "Công Việc Có Sự Thay Đổi"
            ];
            // $sending_for_created_job_person_mail = new SendingMailForCreatedJobPerson($job_info, $content_for_mail);
            // Mail::to($job_info->createdBy->email)->send($sending_for_created_job_person_mail);

            if ( count($data_send_mail) > 0 ) {
                DB::table('queue_mail')->insert($data_send_mail);
            }

            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Từ chối công việc thành công");
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function outJob(Request $request) {
        $job_id = $request->job_id;
        $job_info = Job::with('address', 'building', 'customer', 'createdBy', 'reason_job')->where('id', $job_id)->where('is_deleted', "0")->first();
        if ( ! $job_info) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Công việc không tồn tại");
        }

        $user_info = Auth::user();
        $user_name_info = $user_info->lastname . ' ' . $user_info->firstname . '(' . $user_info->username . ')';
        $user_id = $user_info->id;

        DB::beginTransaction();
        try {
            $data_job_process = [
                'job_id'        => $job_id,
                'created_at'    => date('Y-m-d H:i:s'),
                'created_by'    => $user_id,
                'note'          => $request->reason,
                'action'        => $user_name_info . ' bỏ nhận việc'
            ];
            DB::table('job_proccess')->insert($data_job_process);
            DB::table('job_employees')->where('user_id', $user_id)->where('job_id', $job_id)->update(['status'  => JobEmployee::GAVE_UP]);

            
            // handle save data to sending sms
            $content_for_send_mail = Auth::user()->lastname . " " . Auth::user()->firstname . " đã bỏ công việc " . $job_info->reason_job->value_setting . ". Lý do: " . $request->reason;
            $content_for_sms = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $content_for_send_mail);
            $data_sending_sms = [
                'phone'     => $job_info->createdBy->phone,
                'is_send'   => 1,
                'content'   => $content_for_sms,
                'send_at'   => date('Y-m-d H:i:s')
            ];

            DB::table('sms_queue')->insert($data_sending_sms);

            // handle sending email for created job persion
            // $sending_for_created_job_person_mail = new SendingMailForCreatedJobPerson($job_info, $content_for_send_mail);
            // Mail::to($job_info->createdBy->email)->send($sending_for_created_job_person_mail);

            $html =  view('sending_email.job_update', ['job'    => $job_info, 'content' => $content_for_send_mail])->render();
            $content = htmlspecialchars($html);
            $data_send_mail = [
                'content'   => $content,
                'is_send'   => 2,
                'send_at'   => null,
                'email'     => $job_info->createdBy->email,
                'title'     => "Công Việc Có Sự Thay Đổi"
            ];

            if ( count($data_send_mail) > 0 ) {
                DB::table('queue_mail')->insert($data_send_mail);
            }

            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Bỏ công việc thành công", ['job_id'=> $job_id]);
        } catch (\Throwable $th) {
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }
    
    
    public function markCompleteJob(Request $request) {
        $job_id = $request->job_id;
        $job_info = Job::with('address', 'building', 'customer', 'createdBy', 'reason_job')->where('id', $job_id)->where('is_deleted', "0")->first();
        if ( ! $job_info) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Công việc không tồn tại");
        }

        $user_info = Auth::user();
        $user_name_info = $user_info->lastname . ' ' . $user_info->firstname . '(' . $user_info->username . ')';
        $user_id = $user_info->id;

        DB::beginTransaction();
        try {
            $data_job_process = [
                'job_id'        => $job_id,
                'created_at'    => date('Y-m-d H:i:s'),
                'created_by'    => $user_id,
                'note'          => $request->note,
                'action'        => $user_name_info . ' đánh dấu trạng thái công việc hoàn thành'
            ];
            
            DB::table('job_proccess')->insert($data_job_process);

            DB::table('jobs')->where('id', $job_id)->update(['status'   => Job::COMPLETED]);
            
            // handle save data to sending sms
            $content_for_mail = Auth::user()->lastname . " " . Auth::user()->firstname . " đã dánh dấu hoàn thành công việc " . $job_info->reason_job->value_setting;
            $content_for_sms = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $content_for_mail);
            $data_sending_sms = [
                'phone'     => $job_info->createdBy->phone,
                'is_send'   => 1,
                'content'   => $content_for_sms,
                'send_at'   => date('Y-m-d H:i:s')
            ];
            DB::table('sms_queue')->insert($data_sending_sms);

            // handle sending email for created job persion
            // $sending_for_created_job_person_mail = new SendingMailForCreatedJobPerson($job_info, $content_for_mail);
            // Mail::to($job_info->createdBy->email)->send($sending_for_created_job_person_mail);

            $html =  view('sending_email.job_update', ['job'    => $job_info, 'content' => $content_for_mail])->render();
            $content = htmlspecialchars($html);
            $data_send_mail = [
                'content'   => $content,
                'is_send'   => 2,
                'send_at'   => null,
                'email'     => $job_info->createdBy->email,
                'title'     => "Công Việc Có Sự Thay Đổi"
            ];

            if ( count($data_send_mail) > 0 ) {
                DB::table('queue_mail')->insert($data_send_mail);
            }

            // handle contract status
            if ($job_info->job_type == Job::EQUIPMENT_INSTALLATION && $job_info->status == Job::PROCESSING && $job_info->contract_id) {
                $contract_id = $job_info->contract_id;
                DB::table('contracts')->where('id', $contract_id)->update(['status'   => Contract::ACTIVE]);
            }

            return $this->responseJSONSuccess($this::IS_COMMIT_DB, "Đã chuyển trạng thái hoàn thành", ['job_id' => $job_id]);
        } catch (\Throwable $th) {
            // return $this->responseJSONFalse($this::IS_ROLLBACK_DB, $th->getMessage());
            return $this->responseJSONFalse($this::IS_ROLLBACK_DB, "Lỗi server!");
        }
    }

    public function getListJobProccesses(Request $request) {
        $job_id = $request->job_id;
        if (! $job_id ) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Thiếu mã công việc");
        }

        // handle check $job_id exist 
        $job_info = Job::where('id', $job_id)->where('is_deleted', 0)->first();
        if ( ! $job_info ) {
            return $this->responseJSONFalse($this::IS_NOT_COMMIT_DB, "Công việc không tồn tại");
        }

        $list_job_processes = JobProcess::whereHas('job', function($q) use($job_id) {
                                            $q->where('id', $job_id)
                                                ->where('is_deleted', 0);
                                        })
                                        ->where('job_id', $job_id)
                                        ->get();
        return $this->responseJSONSuccessWithData($this::IS_NOT_COMMIT_DB, "OK", ['list_job_processes'  => $list_job_processes]);
    }
}
