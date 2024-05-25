<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CommentsAppointments;
use App\Models\CourtCase;
use App\Models\SimpleUsers;
use App\Models\AppointmentStatuses;
use App\Models\Appointment;
use App\Models\DocsAppointments;
use App\Models\StatusAppointments;
use App\Models\ThirdParties;
use App\Models\User;
use Bitrix\Im\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;

class AppointmentsController extends Controller
{
    public function createApp(Request $request) {
        $validateApp = Validator::make($request->all(),
            [
//                'appointment_file' => 'required',
                'appointment_file' => 'nullable',
                'plaintiff_id' => 'nullable',
                'defendant_id' => 'nullable',
                'docs_list' => 'nullable',
                'is_plaintiff' => 'nullable',
                'is_defendant' => 'nullable',
                'third_parties' => 'nullable',
                'status_id'=>'nullable',
            ]);

        if($validateApp->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Проверьте введенные данные',
                'errors' => $validateApp->errors()
            ]);
        }

        $user = auth('sanctum')->id();

        $appParamsArr['status_id'] = $request->input('status_id');

        // проверка кто он ответчик или истец
        if (!empty($request->input('is_plaintiff'))) {
            $appParamsArr['plaintiff_id'] = $user;
            $appParamsArr['defendant_id'] = $request->input('defendant_id');
        } elseif (!empty($request->input('is_defendant'))) {
            $appParamsArr['defendant_id'] = $user;
            $appParamsArr['plaintiff_id'] = $request->input('plaintiff_id');
        } else {
            $appParamsArr['plaintiff_id'] = $request->input('plaintiff_id');
            $appParamsArr['defendant_id'] = $request->input('defendant_id');
        }
        // загрузка файла заявки
        if (!empty($request->file('appointment_file'))) {
            $fileName = 'app-'.time().'-'.$user.'.'.$request->file('appointment_file')->getClientOriginalExtension();
            $pathApp = $request->file('appointment_file')->storeAs('',$fileName);
            $appParamsArr['appointment_file'] = $pathApp;
        }

        // поменять на привзяку в xml_id
        $appParamsArr['user_id'] = $user;

        $appointmentAdd = Appointment::create($appParamsArr);

        // загрузка доп файлов

    }
    // модерация заявки канцелярией
    public function moderate(Request $request) {
        $user = auth('sanctum')->id();

        $validateApp = Validator::make($request->all(),
            [
                'appointment_id' => 'required',
                'result'=>'required'
            ]);

        if($validateApp->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Проверьте введенные данные',
                'errors' => $validateApp->errors()
            ]);
        }

        $appUpdate = Appointment::find((int)$request->input('appointment_id'));

        if ($request->input('result')=='success') {
            $appUpdate->status_id = 3;
            $appUpdate->save();
        }
    }

    public static function getAppointmentList() {
        $user = auth('sanctum')->id();

//        $appointments = Appointment::all()->where('user_id',$user)->statuses->with('app_status:id,name')->get();
//        $appointments = Appointment::all()->where('user_id',$user)->sortByDate();
        $appointments = Appointment::select()->where([['user_id',$user],['status_id','!=',11]])->orderBy('id','desc')->get();

        $appointments->each(function (Appointment $item,int $key) {
//            $item->setAttribute('appointment_file',Storage::mimeType($item->getAttribute('appointment_file')));


            $plaintif = User::all()->where('id',$item->getAttribute('plaintiff_id'));
            $defedant = User::all()->where('id',$item->getAttribute('defendant_id'));
            $item->setAttribute('plaintiff_fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
            $item->setAttribute('defendant_fio',$defedant->value('last_name').' '.mb_substr($defedant->value('name'), 0, 1).'.'.mb_substr($defedant->value('second_name'),0,1).'.');

//            $item->setAttribute('plaintiff_id',User::find($item->getAttribute('plaintiff_id')));
//            $item->setAttribute('defendant_id',User::find($item->getAttribute('defendant_id')));
            $status = StatusAppointments::all()->where('id',$item->getAttribute('status_id'));
            $item->setAttribute('status_name',$status->value('name'));

//            if (!empty($item->getAttribute('appointment_file'))) {
//                $item->setAttribute('appointment_file',Storage::disk('local')->get($item->getAttribute('appointment_file')));
//            }



//                = Storage::get($appointment['appointment_file']);
//            dd($item);
        });

//        foreach ($appointments->values()->all() as $key=>$appointment) {
//            dd($appointment);
//            $appointments[$key]['appointment_file'] = Storage::get($appointment['appointment_file']);
//        }

//        $appointments = Appointment::find(1);
//        $status = $appointments->statuses;
//        $appointments->add($appointments->status_id->name);
//        $appointments

        return $appointments;
    }

    public static function getAppointment(Request $request) {
        $user = auth('sanctum')->id();
        $appointments = Appointment::select()->where([['user_id',$user],['id',$request->get('id')]])->get();

        $appointments->each(function (Appointment $item,int $key) {
//            $item->setAttribute('appointment_file',Storage::mimeType($item->getAttribute('appointment_file')));

            $plaintif = User::all()->where('id',$item->getAttribute('plaintiff_id'));
            $defedant = User::all()->where('id',$item->getAttribute('defendant_id'));
            $item->setAttribute('plaintiff_fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
            $item->setAttribute('defendant_fio',$defedant->value('last_name').' '.mb_substr($defedant->value('name'), 0, 1).'.'.mb_substr($defedant->value('second_name'),0,1).'.');

//            $item->setAttribute('plaintiff_name',$plaintif->value(''));
//            $item->setAttribute('plaintiff_name',$plaintif->value(''));
//            $item->setAttribute('defendant_id',$defedant);
            $status = StatusAppointments::all()->where('id',$item->getAttribute('status_id'));
            $item->setAttribute('status_name',$status->value('name'));



//                = Storage::get($appointment['appointment_file']);
//            dd($item);
        });

        return $appointments;
    }

    public function editApp(Request $request) {
        $validateApp = Validator::make($request->all(),
            [
//                'appointment_file' => 'required',
                'appointment_file' => 'nullable',
                'plaintiff_id' => 'nullable',
                'defendant_id' => 'nullable',
                'docs_list' => 'nullable',
                'is_plaintiff' => 'nullable',
                'is_defendant' => 'nullable',
                'third_parties' => 'nullable',
            ]);

        if($validateApp->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Проверьте введенные данные',
                'errors' => $validateApp->errors()
            ]);
        }

        $user = auth('sanctum')->id();

        $appParamsArr['status_id'] = 1;

        // проверка кто он ответчик или истец
        if (!empty($request->input('is_plaintiff'))) {
            $appParamsArr['plaintiff_id'] = $user;
            $appParamsArr['defendant_id'] = $request->input('defendant_id');
        } elseif (!empty($request->input('is_defendant'))) {
            $appParamsArr['defendant_id'] = $user;
            $appParamsArr['plaintiff_id'] = $request->input('plaintiff_id');
        } else {
            $appParamsArr['plaintiff_id'] = $request->input('plaintiff_id');
            $appParamsArr['defendant_id'] = $request->input('defendant_id');
        }
        // загрузка файла заявки
        if (!empty($request->file('appointment_file'))) {
            $fileName = 'app-'.time().'-'.$user.'.'.$request->file('appointment_file')->getClientOriginalExtension();
            $pathApp = $request->file('appointment_file')->storeAs('',$fileName);
            $appParamsArr['appointment_file'] = $pathApp;
        }

        // поменять на привзяку в xml_id
        $appParamsArr['user_id'] = $user;

//        $appointmentAdd = Appointment::create($appParamsArr);

        $appUpdate = Appointment::find((int)$request->input('id'));

        $appUpdate->fill($appParamsArr);
        $appUpdate->save();

    }

    public function changeStatus(Request $request) {
        $user = auth('sanctum')->id();

        $validateApp = Validator::make($request->all(),
            [
                'id' => 'required',
                'status'=>'required'
            ]);

        if($validateApp->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Проверьте введенные данные',
                'errors' => $validateApp->errors()
            ]);
        }

        $appUpdate = Appointment::find((int)$request->input('id'));

//        if ($request->input('status')==) {
            $appUpdate->status_id = $request->input('status');
            $appUpdate->save();
//        }
    }

    // FOR CHANCELLERY
    public static function getAppointmentListChancellery() {
        $user = auth('sanctum')->id();

        $appointments = Appointment::select()->where([['status_id',1]])->orderBy('id','desc')->get();

        $appointments->each(function (Appointment $item,int $key) {
            $plaintif = User::all()->where('id',$item->getAttribute('plaintiff_id'));
            $defedant = User::all()->where('id',$item->getAttribute('defendant_id'));
            $item->setAttribute('plaintiff_fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
            $item->setAttribute('defendant_fio',$defedant->value('last_name').' '.mb_substr($defedant->value('name'), 0, 1).'.'.mb_substr($defedant->value('second_name'),0,1).'.');
            $status = StatusAppointments::all()->where('id',$item->getAttribute('status_id'));
            $item->setAttribute('status_name',$status->value('name'));
        });
        return $appointments;
    }
    // detail
    public static function getAppointmentChancellery(Request $request) {
        $user = auth('sanctum')->id();
        $appointments = Appointment::select()->where([['id',$request->get('id')]])->get();

        $appointments->each(function (Appointment $item,int $key) {

            $plaintif = User::all()->where('id',$item->getAttribute('plaintiff_id'));
            $defedant = User::all()->where('id',$item->getAttribute('defendant_id'));
            $item->setAttribute('plaintiff_fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
            $item->setAttribute('defendant_fio',$defedant->value('last_name').' '.mb_substr($defedant->value('name'), 0, 1).'.'.mb_substr($defedant->value('second_name'),0,1).'.');

            $status = StatusAppointments::all()->where('id',$item->getAttribute('status_id'));
            $item->setAttribute('status_name',$status->value('name'));
        });

        return $appointments;
    }
    // edit
    public static function editAppChancellery(Request $request) {

        $validateApp = Validator::make($request->all(),
            [
                'id' => 'required',
                'status'=>'required',
                'comment'=>'nullable'
            ]);

        if($validateApp->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Проверьте введенные данные',
                'errors' => $validateApp->errors()
            ]);
        }
//        dd($request->input('comment'));
        $appUpdate = Appointment::find((int)$request->input('id'));

        $appUpdate->status_id = $request->input('status');
        $appUpdate->save();

        $test = [
            'comment'=>$request->input('comment'),
            'ch_user_id'=>auth('sanctum')->id(),
            'appointment_id'=>(int)$request->input('id'),
        ];
        $comments = CommentsAppointments::create($test);
    }

    // FOR COURTADMIN
    public static function getAppointmentListAdmin() {
        $user = auth('sanctum')->id();

        $appointments = Appointment::select()->where([['status_id',13]])->orderBy('id','desc')->get();

        $appointments->each(function (Appointment $item,int $key) {
            $plaintif = User::all()->where('id',$item->getAttribute('plaintiff_id'));
            $defedant = User::all()->where('id',$item->getAttribute('defendant_id'));
            $item->setAttribute('plaintiff_fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
            $item->setAttribute('defendant_fio',$defedant->value('last_name').' '.mb_substr($defedant->value('name'), 0, 1).'.'.mb_substr($defedant->value('second_name'),0,1).'.');
            $status = StatusAppointments::all()->where('id',$item->getAttribute('status_id'));
            $item->setAttribute('status_name',$status->value('name'));
        });
        return $appointments;
    }
    // detail
    public static function getAppointmentCourtAdmin(Request $request) {
        $user = auth('sanctum')->id();
        $appointments = Appointment::select()->where([['id',$request->get('id')]])->get();

        $appointments->each(function (Appointment $item,int $key) {

            $plaintif = User::all()->where('id',$item->getAttribute('plaintiff_id'));
            $defedant = User::all()->where('id',$item->getAttribute('defendant_id'));
            $item->setAttribute('plaintiff_fio',$plaintif->value('last_name').' '.mb_substr($plaintif->value('name'), 0, 1).'.'.mb_substr($plaintif->value('second_name'),0,1).'.');
            $item->setAttribute('defendant_fio',$defedant->value('last_name').' '.mb_substr($defedant->value('name'), 0, 1).'.'.mb_substr($defedant->value('second_name'),0,1).'.');

            $status = StatusAppointments::all()->where('id',$item->getAttribute('status_id'));
            $item->setAttribute('status_name',$status->value('name'));

            # Получение комментария
            $comment = CommentsAppointments::select()->where('appointment_id',$item->getAttribute('id'))->orderBy('id','desc')->first();
            if (!empty($comment)) {
                $item->setAttribute('comment',empty($comment->value('comment')) ? '' : $comment->value('comment'));
            }
        });

        return $appointments;
    }
    // edit
    public static function editAppCourtAdmin(Request $request) {

        $validateApp = Validator::make($request->all(),
            [
                'id' => 'required',
                'status'=>'required',
            ]);

        if($validateApp->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Проверьте введенные данные',
                'errors' => $validateApp->errors()
            ]);
        }
        $appUpdate = Appointment::find((int)$request->input('id'));

        if ($request->input('status')==3) {
            # создание судебного дела
            $courtArr = [
                'app_id'=>(int)$request->input('id'),
                'judge_id'=>$request->input('judge_id'),
                'material_num'=>$request->input('material_num'),
                'case_number'=>$request->input('case_number'),
                'date_start'=>date('Y-m-d'),
                'status'=>2,
                'defendant_id'=>$appUpdate->defendant_id,
                'plaintiff_id'=>$appUpdate->plaintiff_id,
            ];
            $res = CourtCase::create($courtArr);
        }

        $appUpdate->status_id = $request->input('status');
        $appUpdate->save();
    }
}
