<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CommentsAppointments;
use App\Models\CourtCase;
use App\Models\CourtCaseStatuses;
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

class CourtCaseController extends Controller
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
    public static function getCourtCasesList() {
        $user = auth('sanctum')->id();

        $courtCase = CourtCase::select()->where([['judge_id',$user],['status','!=',4]])->orderBy('id','desc')->get();

        if ($courtCase->count()>1) {
            $courtCase->each(function (CourtCase $item, int $key) {
                $plaintif = User::all()->where('id', $item->getAttribute('plaintiff_id'));
                $defedant = User::all()->where('id', $item->getAttribute('defendant_id'));
                $item->setAttribute('plaintiff_fio', $plaintif->value('last_name') . ' ' . mb_substr($plaintif->value('name'), 0, 1) . '.' . mb_substr($plaintif->value('second_name'), 0, 1) . '.');
                $item->setAttribute('defendant_fio', $defedant->value('last_name') . ' ' . mb_substr($defedant->value('name'), 0, 1) . '.' . mb_substr($defedant->value('second_name'), 0, 1) . '.');

                $status = CourtCaseStatuses::all()->where('id', $item->getAttribute('status'));
                $item->setAttribute('status_name', $status->value('name'));
                $item->setAttribute('status_id', $item->value('status'));

            });
        }
        else {
            $plaintif = User::all()->where('id', $courtCase->value('plaintiff_id'));
//            dd($plaintif);
            $defedant = User::all()->where('id', $courtCase->value('defendant_id'));
//            $courtCase->setAttribute('plaintiff_fio', $plaintif->value('last_name') . ' ' . mb_substr($plaintif->value('name'), 0, 1) . '.' . mb_substr($plaintif->value('second_name'), 0, 1) . '.');
//            $courtCase->setAttribute('defendant_fio', $defedant->value('last_name') . ' ' . mb_substr($defedant->value('name'), 0, 1) . '.' . mb_substr($defedant->value('second_name'), 0, 1) . '.');
            $status = CourtCaseStatuses::all()->where('id', $courtCase->value('status'));
            $arr[] = [
                'plaintiff_fio'=>$plaintif->value('last_name') . ' ' . mb_substr($plaintif->value('name'), 0, 1) . '.' . mb_substr($plaintif->value('second_name'), 0, 1) . '.',
                'defendant_fio'=>$defedant->value('last_name') . ' ' . mb_substr($defedant->value('name'), 0, 1) . '.' . mb_substr($defedant->value('second_name'), 0, 1) . '.',
                'id'=>$courtCase->value('id'),
                'status_name'=> $status->value('name'),
                'created_at'=>$courtCase->value('date_start'),
                'status_id'=>$courtCase->value('status'),
                'plaintiff_id'=>$courtCase->value('plaintiff_id'),
                'defendant_id'=>$courtCase->value('defendant_id'),
                'case_number'=>$courtCase->value('case_number'),
                'material_num'=>$courtCase->value('material_num'),

            ];
            return $arr;
        }
        return $courtCase;
    }

    public static function getCourtesDetail(Request $request) {
        $user = auth('sanctum')->id();
        $courtCase = CourtCase::select()->where([['judge_id',$user],['id',$request->get('id')]])->get();

        $courtCase->each(function (CourtCase $item,int $key) {
//            $item->setAttribute('appointment_file',Storage::mimeType($item->getAttribute('appointment_file')));

            $plaintif = User::all()->where('id',$item->getAttribute('plaintiff_id'));
            $defedant = User::all()->where('id',$item->getAttribute('defendant_id'));
            $item->setAttribute('plaintiff_fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
            $item->setAttribute('defendant_fio',$defedant->value('last_name').' '.mb_substr($defedant->value('name'), 0, 1).'.'.mb_substr($defedant->value('second_name'),0,1).'.');

            $status = CourtCaseStatuses::all()->where('id',$item->getAttribute('status'));
            $item->setAttribute('status_name',$status->value('name'));
        });

        return $courtCase;
    }



    public static function getCourtCasesListSU() {
        $user = auth('sanctum')->id();

        $courtCase = CourtCase::select()->where([['plaintiff_id',$user],['status','!=',4]])->orWhere([['defendant_id',$user],['status','!=',4]])->orderBy('id','desc')->get();

        if ($courtCase->count()>1) {
            $courtCase->each(function (CourtCase $item, int $key) {
                $plaintif = User::all()->where('id', $item->getAttribute('plaintiff_id'));
                $defedant = User::all()->where('id', $item->getAttribute('defendant_id'));
                $judge = User::all()->where('id', $item->getAttribute('judge_id'));
                $item->setAttribute('plaintiff_fio', $plaintif->value('last_name') . ' ' . mb_substr($plaintif->value('name'), 0, 1) . '.' . mb_substr($plaintif->value('second_name'), 0, 1) . '.');
                $item->setAttribute('defendant_fio', $defedant->value('last_name') . ' ' . mb_substr($defedant->value('name'), 0, 1) . '.' . mb_substr($defedant->value('second_name'), 0, 1) . '.');
                $item->setAttribute('judge_fio', $judge->value('last_name') . ' ' . mb_substr($judge->value('name'), 0, 1) . '.' . mb_substr($judge->value('second_name'), 0, 1) . '.');

                $status = CourtCaseStatuses::all()->where('id', $item->getAttribute('status'));
                $item->setAttribute('status_name', $status->value('name'));
                $item->setAttribute('status_id', $item->value('status'));

            });
        }
        else {
            $plaintif = User::all()->where('id', $courtCase->value('plaintiff_id'));
//            dd($plaintif);
            $defedant = User::all()->where('id', $courtCase->value('defendant_id'));
            $judge = User::all()->where('id', $courtCase->value('judge_id'));
//            $courtCase->setAttribute('plaintiff_fio', $plaintif->value('last_name') . ' ' . mb_substr($plaintif->value('name'), 0, 1) . '.' . mb_substr($plaintif->value('second_name'), 0, 1) . '.');
//            $courtCase->setAttribute('defendant_fio', $defedant->value('last_name') . ' ' . mb_substr($defedant->value('name'), 0, 1) . '.' . mb_substr($defedant->value('second_name'), 0, 1) . '.');
            $status = CourtCaseStatuses::all()->where('id', $courtCase->value('status'));
            $arr[] = [
                'plaintiff_fio'=>$plaintif->value('last_name') . ' ' . mb_substr($plaintif->value('name'), 0, 1) . '.' . mb_substr($plaintif->value('second_name'), 0, 1) . '.',
                'defendant_fio'=>$defedant->value('last_name') . ' ' . mb_substr($defedant->value('name'), 0, 1) . '.' . mb_substr($defedant->value('second_name'), 0, 1) . '.',
                'judge_fio'=>$judge->value('last_name') . ' ' . mb_substr($judge->value('name'), 0, 1) . '.' . mb_substr($judge->value('second_name'), 0, 1) . '.',
                'id'=>$courtCase->value('id'),
                'status_name'=> $status->value('name'),
                'created_at'=>$courtCase->value('date_start'),
                'status_id'=>$courtCase->value('status'),
                'plaintiff_id'=>$courtCase->value('plaintiff_id'),
                'defendant_id'=>$courtCase->value('defendant_id'),
                'case_number'=>$courtCase->value('case_number'),
                'material_num'=>$courtCase->value('material_num'),

            ];
            return $arr;
        }
        return $courtCase;
    }

    public static function getCourtesDetailSU(Request $request) {
        $user = auth('sanctum')->id();
        $courtCase = CourtCase::select()->where([['id',$request->get('id')]])->get();

        $courtCase->each(function (CourtCase $item,int $key) {
//            $item->setAttribute('appointment_file',Storage::mimeType($item->getAttribute('appointment_file')));

            $plaintif = User::all()->where('id',$item->getAttribute('plaintiff_id'));
            $defedant = User::all()->where('id',$item->getAttribute('defendant_id'));
            $item->setAttribute('plaintiff_fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
            $item->setAttribute('defendant_fio',$defedant->value('last_name').' '.mb_substr($defedant->value('name'), 0, 1).'.'.mb_substr($defedant->value('second_name'),0,1).'.');

            $status = CourtCaseStatuses::all()->where('id',$item->getAttribute('status'));
            $item->setAttribute('status_name',$status->value('name'));
        });

        return $courtCase;
    }
}
