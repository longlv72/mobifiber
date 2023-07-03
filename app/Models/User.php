<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    //column real_manage_unit
    public const BUSSINESS_COMPANY = 1; // CÔNG TY KINH DOANH
    public const NETWORK_CENTER = 2; // TRUNG TÂM MẠNG LƯỚI
    public const AGENT = 3; // ĐẠI LÝ

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role() : BelongsTo {
        return $this->belongsTo(RoleGroup::class, 'role_group_id', 'id');
    }

    public function hasJobs() : BelongsToMany {
        return $this->belongsToMany(Job::class, 'job_employees', 'user_id', 'job_id');
    }
}
