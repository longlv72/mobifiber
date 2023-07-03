<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;

    //column job_type
    public const SURVEY = 1; // khảo sát
    public const SOLVE_PROBLEM = 2; // giải quyết vấn đề
    public const HANDLING_FEEDBACK = 3; // xử lý phản ánh KH
    public const EQUIPMENT_INSTALLATION = 41; // Lắp đặt thiết bị

    // column status
    public const NOT_ASSIGN_YET = 1; // chưa giao việc
    public const ASSIGNED = 2; // đã giao chưa ai nhận
    public const PROCESSING = 3; //đã có người nhận - đang xử lý 
    public const COMPLETED = 4; // đã hoàn thành
    public const UNACHIEVABLE = 5; // Không khả thi, không thể thực hiện
    public const ASSIGNED_BUT_BE_DENIED = 6; // đã giao nhưng bị từ chối


    public function address() : BelongsTo {
        return $this->belongsTo(CustomerAddress::class, 'address_id', 'id');
    }
    public function created_by() : BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function createdBy() : BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    
    public function updated_by() : BelongsTo {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function assigned_employee_groups() : BelongsToMany {
        return $this->belongsToMany(User::class, 'job_employees', 'job_id','user_id')->wherePivot('status', JobEmployee::WORKED); 
    }

    public function reject_or_gave_up_employees() : BelongsToMany {
        return $this->belongsToMany(User::class, 'job_employees', 'job_id','user_id')->wherePivot('status', JobEmployee::REJECTED)->orWhere('status', JobEmployee::GAVE_UP); 
    }

    public function building() : BelongsTo {
        return $this->belongsTo(Building::class, 'building_id', 'id');
    }

    public function customer() : BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function job_processes() : HasMany {
        return $this->hasMany(JobProcess::class, 'job_id', 'id');
    }

    public function job_type() : BelongsTo {
        return $this->belongsTo(Setting::class, 'job_type', 'id');
    }

    public function reason_job() : BelongsTo {
        return $this->belongsTo(Setting::class, 'reason', 'id')->whereNotNull('obj_id');
    }

    public function contract() : BelongsTo {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function listWorkedEmployee() : BelongsToMany {
        return $this->belongsToMany(User::class, 'job_employees', 'job_id','user_id')->wherePivot('status', JobEmployee::WORKED); 
    }
    
    public function listRejectedEmployee() : BelongsToMany {
        return $this->belongsToMany(User::class, 'job_employees', 'job_id','user_id')->wherePivot('status', JobEmployee::REJECTED); 
    }

    public function listGaveUpEmployee() : BelongsToMany {
        return $this->belongsToMany(User::class, 'job_employees', 'job_id','user_id')->wherePivot('status', JobEmployee::GAVE_UP); 
    }
}
