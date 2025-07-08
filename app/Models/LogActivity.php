<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivity extends Model
{
    protected $fillable = [
        'user_id',
        'post_id',
        'description',
        'log_type'
    ];
}
