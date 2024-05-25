<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpleUsers extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_register', 'passport_seria', 'passport_num','user_id'
    ];
}
