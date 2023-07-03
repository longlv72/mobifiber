<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobProcess extends Model
{
    use HasFactory;

    protected $table = 'job_proccess';

    // column status
    public const PROCESSING = 1;
    public const COMPLETED = 2;
    public const CANCELED = 3;

    public function job() : BelongsTo {
        return $this->belongsTo(Job::class, 'job_id', 'id');
    }

    public function comments() : HasMany {
        return $this->hasMany(Comment::class, 'job_proccess_id', 'id');
    }
}
