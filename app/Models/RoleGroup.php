<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleGroup extends Model
{
    use HasFactory;

    protected $table = 'role_groups';
    protected $fillable = ['role_name'];
    public const ADMIN = 1;
    public const EMPLOYEE = 2;
    public const ENGINEER_EMPLOYEE = 2;
}
