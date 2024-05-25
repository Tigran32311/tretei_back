<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    protected $table = 'meeting';

    protected $fillable = [
        'court_case_id','date_meeting','result_id','description','protocol_id','status_id','address','time_meeting'
    ];
}
