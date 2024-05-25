<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chancellery extends Model
{
    use HasFactory;

    protected $table = 'chancellery';

    protected $fillable = [
        'name','file_link','app_id'
    ];
}
