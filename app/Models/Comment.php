<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'comments';

    public function children() : HasMany {
        return $this->hasMany(Comment::class, 'parent_id')->whereNotNull('parent_id');
    }

    public function parent() : BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id')->whereNull('parent_id');
    }

    public function created_by() : BelongsTo {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
