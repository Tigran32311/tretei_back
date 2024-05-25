<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocsAppointments extends Model
{
    use HasFactory;

    protected $table = 'docs_app';

    protected $fillable = [
        'name','file_link','app_id'
    ];
}
