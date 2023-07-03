<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    // status
    public const WAITING_SIGN = 1;
    public const ACTIVE = 2;
    public const EXPIRED = 3;

    public function package() : BelongsTo {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }
    public function device() : BelongsTo {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }
    public function partner() : BelongsTo {
        return $this->belongsTo(Partner::class, 'partner_id', 'id');
    }
    public function customer() : BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function building() : BelongsTo {
        return $this->belongsTo(Building::class, 'buildings_id', 'id');
    }

    public function jobs() : HasMany {
        return $this->hasMany(Job::class, 'contract_id', 'id')->where('is_deleted', 0);
    }
}
