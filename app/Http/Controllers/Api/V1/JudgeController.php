<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CommentsAppointments;
use App\Models\SimpleUsers;
use App\Models\AppointmentStatuses;
use App\Models\Appointment;
use App\Models\DocsAppointments;
use App\Models\StatusAppointments;
use App\Models\Judges;
use App\Models\User;
use Bitrix\Im\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class JudgeController extends Controller
{
    public function getUsersToSelect(Request $request)
    {
        $simple = Judges::all();

        if ($simple->count()>1) {
            $simple->each(function (Judges $item,int $key) {
                $plaintif = User::all()->where('id',$item->getAttribute('user_id'))->where('role','judge');
                if ($plaintif->count()>0) {
                    $item->setAttribute('fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
                } else {
                    $item->delete();
                }
            });
        } else {
            $plaintif = User::all()->where('id',$simple->value('user_id'))->where('role','judge');
//            $simple->put('id',$simple->value('id'));

            if ($plaintif->count()>0) {
                $arr[] = [
                    'id'=>$simple->value('user_id'),
                    'fio'=>$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.',
                ];
                return $arr;
//                $simple->put('fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
            } else {
                return array();
            }
        }
        return $simple;
    }
}
