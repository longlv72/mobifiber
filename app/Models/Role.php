<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = ['role_add', 'role_edit', 'role_delete', 'role_view', 'role_import', 'role_export', 'role_report'];

    public function module() : BelongsTo {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }
}
