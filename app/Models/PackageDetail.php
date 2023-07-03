<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageDetail extends Model
{
    use HasFactory;

    protected $table = 'package_detail';

    public function packages() : BelongsTo {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }


}
