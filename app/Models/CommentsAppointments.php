<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommentsAppointments extends Model
{
    use HasFactory;

    protected $table = 'comments_appointment';

    protected $fillable = [
        'ch_user_id','comment','appointment_id'
    ];
}
