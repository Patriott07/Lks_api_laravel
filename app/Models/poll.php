<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class poll extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = [
        'title', 'description', 'deadline', 'created_by'
    ];

    /**
     * Get the user that owns the poll
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
