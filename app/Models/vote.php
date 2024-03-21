<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class vote extends Model
{
    use HasFactory;

    public $fillable = ['user_id', 'poll_id', 'division_id', 'choice_id'];
}
