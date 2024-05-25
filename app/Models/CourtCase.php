<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtCase extends Model
{
    use HasFactory;

    protected $table = 'courtCase';

    protected $fillable = [
        'app_id','judge_id','material_num','case_number','date_start','status','defendant_id','plaintiff_id','created_at','status_id'
    ];
}
