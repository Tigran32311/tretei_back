<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CommentsAppointments;
use App\Models\CourtCase;
use App\Models\CourtCaseStatuses;
use App\Models\Meeting;
use App\Models\MeetingStatuses;
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

class MeetingController extends Controller
{
    public function createMeeting(Request $request) {
        $user = auth('sanctum')->id();

        $appParamsArr['status_id'] = $request->input('status_id');
        $appParamsArr['address'] = $request->input('address');
//        $appParamsArr['time_meeting'] = $request->input('time_meeting');
        $appParamsArr['time_meeting'] = date('H:i:s',strtotime($request->input('time_meeting')));
        $appParamsArr['date_meeting'] = $request->input('date_meeting');
        $appParamsArr['court_case_id'] = $request->input('court_id');


        // поменять на привзяку в xml_id
//        $appParamsArr['user_id'] = $user;

        $appointmentAdd = Meeting::create($appParamsArr);

        // загрузка доп файлов

    }

    public static function getMeetingList() {
        $user = auth('sanctum')->id();

        $meeting = Meeting::select()->orderBy('date_meeting','desc')->get();

        if ($meeting->count()>1) {
            $meeting->each(function (Meeting $item, int $key) {
                $status = MeetingStatuses::all()->where('id', $item->getAttribute('status_id'));
                $court = CourtCase::all()->where('id', $item->getAttribute('court_case_id'));
                $item->setAttribute('status_name', $status->value('name'));
                $item->setAttribute('case_number', $court->value('case_number'));
                $item->setAttribute('material_num', $court->value('material_num'));

            });
        }
        else {
            $status = MeetingStatuses::all()->where('id', $meeting->value('status_id'));
            $court = CourtCase::all()->where('id', $meeting->getAttribute('court_case_id'));
            $arr[] = [
                'id'=>$meeting->value('id'),
                'status_name'=> $status->value('name'),
                'status_id'=>$meeting->value('status'),
                'address'=>$meeting->value('address'),
                'time_meeting'=>$meeting->value('time_meeting'),
                'date_meeting'=>$meeting->value('date_meeting'),
                'material_num'=>$court->value('material_num'),
                'case_number'=>$court->value('case_number'),
            ];
            return $arr;
        }
        return $meeting;
    }

    public static function getMeetingDetail(Request $request) {
        $user = auth('sanctum')->id();

        $meeting = Meeting::select()->where('id',$request->get('id'))->get();

        $meeting->each(function (Meeting $item, int $key) {
            $status = MeetingStatuses::all()->where('id', $item->getAttribute('status_id'));
            $court = CourtCase::all()->where('id', $item->getAttribute('court_case_id'));
            $item->setAttribute('status_name', $status->value('name'));
            $item->setAttribute('case_number', $court->value('case_number'));
            $item->setAttribute('material_num', $court->value('material_num'));
        });
        return $meeting;
    }

    public static function editMeeting(Request $request) {
        $appUpdate = Meeting::find((int)$request->input('id'));

        if ($request->input('status')==2) {
            # создание судебного дела
//            $courtArr = [
//                'material_num'=>$request->input('material_num'),
//                'case_number'=>$request->input('case_number'),
//                'date_start'=>date('Y-m-d'),
//                'status'=>2,
//                'defendant_id'=>$appUpdate->defendant_id,
//                'plaintiff_id'=>$appUpdate->plaintiff_id,
//            ];
            $appUpdate->description = $request->input('description');
            $appUpdate->result_id = (int)$request->input('result_id');
            $appUpdate->status_id = $request->input('status');
//            $appUpdate->status_id = $request->input('status');
            $appUpdate->save();

            # Изменение судебного дела
            if ($request->input('result_id')==2) {
                $court = CourtCase::find((int)$request->input('court_id'));
                $court->result_text = $request->input('result_text');
                $court->status = 1;
                $court->save();
            }
        } else {
            $appUpdate->status_id = $request->input('status');
            $appUpdate->save();
        }

    }

    public static function getMeetingListSU() {
        $user = auth('sanctum')->id();

        $meeting = Meeting::select()->orderBy('date_meeting','desc')->get();

        if ($meeting->count()>1) {
            $meeting->each(function (Meeting $item, int $key) {
                $status = MeetingStatuses::all()->where('id', $item->getAttribute('status_id'));
                $court = CourtCase::all()->where('id', $item->getAttribute('court_case_id'));
                $item->setAttribute('status_name', $status->value('name'));
                $item->setAttribute('case_number', $court->value('case_number'));
                $item->setAttribute('material_num', $court->value('material_num'));
            });
        }
        else {
            $status = MeetingStatuses::all()->where('id', $meeting->value('status_id'));
            $court = CourtCase::all()->where('id', $meeting->getAttribute('court_case_id'));
            $arr[] = [
                'id'=>$meeting->value('id'),
                'status_name'=> $status->value('name'),
                'status_id'=>$meeting->value('status'),
                'address'=>$meeting->value('address'),
                'time_meeting'=>$meeting->value('time_meeting'),
                'date_meeting'=>$meeting->value('date_meeting'),
                'material_num'=>$court->value('material_num'),
                'case_number'=>$court->value('case_number'),
            ];
            return $arr;
        }
        return $meeting;
    }

    public static function getMeetingDetailSU(Request $request) {
        $user = auth('sanctum')->id();

        $meeting = Meeting::select()->where('id',$request->get('id'))->get();

        $meeting->each(function (Meeting $item, int $key) {
            $status = MeetingStatuses::all()->where('id', $item->getAttribute('status_id'));
            $court = CourtCase::all()->where('id', $item->getAttribute('court_case_id'));
            $item->setAttribute('status_name', $status->value('name'));
            $item->setAttribute('case_number', $court->value('case_number'));
            $item->setAttribute('material_num', $court->value('material_num'));
        });
        return $meeting;
    }
}
