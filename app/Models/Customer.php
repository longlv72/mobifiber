<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    public function jobs() : HasMany {
        return $this->hasMany(Job::class, 'customer_id', 'id')->where('is_deleted', 0);
    }

    public function addresses() : HasMany {
        return $this->hasMany(CustomerAddress::class, "customer_id", "id")->where('is_deleted', 0);
    }
}
