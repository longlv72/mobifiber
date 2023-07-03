<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Package extends Model
{
    use HasFactory;
    protected $table = 'packages';
    
    public function updatedBy() : BelongsTo {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function createdBy() : BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function package_detail() : HasOne {
        // return $this->hasOne(PackageDetail::class, 'package_id', 'id')->latestOfMany();
        return $this->hasOne(PackageDetail::class, 'package_id', 'id')->whereDate('start_date','<=', date('Y-m-d'))->whereDate('end_date','>=', date('Y-m-d'));;
    }

    public function package_details() : HasMany {
        return $this->hasMany(PackageDetail::class, 'package_id', 'id');
    }
}
