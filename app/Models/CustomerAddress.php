<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $table = 'customer_addresses';

    public function customer() : BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function jobs() : HasMany {
        return $this->hasMany(Job::class, "address_id", "id");
    }
}
