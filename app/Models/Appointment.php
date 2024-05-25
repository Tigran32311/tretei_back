<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointment';

    protected $fillable = [
        'appointment_file', 'user_id', 'plaintiff_id','defendant_id','status_id'
    ];

    public function statuses():BelongsTo {
        return $this->belongsTo(StatusAppointments::class,'status_id');
    }
//    public function statuses():HasMany {
//        return $this->hasMany(StatusAppointments::class,'status_id');
//    }
}
