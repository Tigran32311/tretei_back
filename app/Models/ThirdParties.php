<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThirdParties extends Model
{
    use HasFactory;

    protected $table = 'third_parties';

    protected $fillable = [
        'user_id','app_id'
    ];
}
