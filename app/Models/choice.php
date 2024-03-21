<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class choice extends Model
{
    use HasFactory;

    public $fillable = ['choices', 'poll_id'];
}
