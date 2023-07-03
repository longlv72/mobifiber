<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $table = 'buildings';

    public function jobs() : HasMany {
        return $this->hasMany(Job::class, "building_id", "id");
    }

    public function contract_await_sign() : HasMany {
        return $this->hasMany(Contract::class, "buildings_id", "id")->where('status', Contract::WAITING_SIGN)->where('is_deleted', 0);
    }
    public function contract_active() : HasMany {
        return $this->hasMany(Contract::class, "buildings_id", "id")->where('status', Contract::ACTIVE)->where('is_deleted', 0);
    }
    public function contract_expired() : HasMany {
        return $this->hasMany(Contract::class, "buildings_id", "id")->where('status', Contract::EXPIRED)->where('is_deleted', 0);
    }
    
}
