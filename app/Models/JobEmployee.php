<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobEmployee extends Model
{
    use HasFactory;

    protected $table = 'job_employees';

    public const WORKED = 1; // đã làm việc từ đầu đến cuối

    public const REJECTED = 2; // được giao nhưng từ chối nhận việc

    public const GAVE_UP = 3; // đã nhận việc nhưng bỏ công việc
}
