<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $table = 'partners';

    // columns type_cooperate
    public const SELF_MOBIFONE = 1; // Mobifone tự triển khai
    public const A_PART = 2; // Một phần
    public const FULL = 3; // Toàn trình

    protected $fillable = [
        'partner_name',
        'email',
        'phone',
        'address',
        'type',
        'is_active',
        'partner_code',
        'created_at',

    ];
}
