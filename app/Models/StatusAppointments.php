<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusAppointments extends Model
{
    use HasFactory;

    protected $table = 'app_status';

    protected $fillable = [
        'appointment_file', 'user_id', 'plaintiff_id','defendant_id','status_id'
    ];

    public function appointments():HasMany {
        return $this->hasMany(Appointment::class);
    }

//    public function appointments():BelongsTo {
//        return $this->belongsTo(Appointment::class);
//    }
}

