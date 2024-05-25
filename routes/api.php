<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "Api" middleware group. Make something great!
|
*/
// группировка запросов
Route::prefix('v1')->group(function () {
    // проверка на авторизацию
    Route::group(['middleware' => ['auth:sanctum']], function(){
        // с проверкой на роль
        Route::apiResource('/simple',V1\SimpleUsersController::class)->middleware('restrictRole:simpleUser');
        Route::post('/simple/getUsersToSelect',[V1\SimpleUsersController::class,'getUsersToSelect'])->middleware('restrictRole:simpleUser');

        // FOR USER
        Route::get('/getAppointment',[V1\AppointmentsController::class,'getAppointmentList'])->middleware('restrictRole:simpleUser');
        Route::post('/getAppointment/detail',[V1\AppointmentsController::class,'getAppointment'])->middleware('restrictRole:simpleUser');
        Route::post('/create_app',[V1\AppointmentsController::class,'createApp'])->middleware('restrictRole:simpleUser');
        Route::post('/edit_app',[V1\AppointmentsController::class,'editApp'])->middleware('restrictRole:simpleUser');
        Route::post('/change_status_app',[V1\AppointmentsController::class,'changeStatus'])->middleware('restrictRole:simpleUser');
        Route::get('/user',[V1\SimpleUsersController::class,'getById']);
        Route::get('/getRole',[V1\UserController::class,'getRole']);
        Route::get('/courtCase/list',[V1\CourtCaseController::class,'getCourtCasesListSU'])->middleware('restrictRole:simpleUser');
        Route::post('/courtCase/detail',[V1\CourtCaseController::class,'getCourtesDetailSU'])->middleware('restrictRole:simpleUser');
        Route::get('/meeting/list',[V1\MeetingController::class,'getMeetingListSU'])->middleware('restrictRole:simpleUser');
        Route::post('/meeting/detail',[V1\MeetingController::class,'getMeetingDetailSU'])->middleware('restrictRole:simpleUser');

        // FOR CHANCELLERY
        Route::get('/getAppointmentListChancellery',[V1\AppointmentsController::class,'getAppointmentListChancellery'])->middleware('restrictRole:chancellery');
        Route::post('/getAppointmentChancellery/detail',[V1\AppointmentsController::class,'getAppointmentChancellery'])->middleware('restrictRole:chancellery');
        Route::post('/moderateAppointment',[V1\AppointmentsController::class,'editAppChancellery'])->middleware('restrictRole:chancellery');

        // FOR COURTADMIN
        Route::prefix('courtAdmin')->group(function () {
            Route::get('/getAppointmentList',[V1\AppointmentsController::class,'getAppointmentListAdmin'])->middleware('restrictRole:courtAdmin');
            Route::post('/getAppointment/detail',[V1\AppointmentsController::class,'getAppointmentCourtAdmin'])->middleware('restrictRole:courtAdmin');
            Route::post('/getAppointment/edit',[V1\AppointmentsController::class,'editAppCourtAdmin'])->middleware('restrictRole:courtAdmin');
            Route::post('/judges/list',[V1\JudgeController::class,'getUsersToSelect'])->middleware('restrictRole:courtAdmin');
        });

        // FOR JUDGE
        Route::prefix('judge')->group(function () {
            Route::get('/courtCase/list',[V1\CourtCaseController::class,'getCourtCasesList'])->middleware('restrictRole:judge');
            Route::post('/courtCase/detail',[V1\CourtCaseController::class,'getCourtesDetail'])->middleware('restrictRole:judge');
            Route::post('/meeting/add',[V1\MeetingController::class,'createMeeting'])->middleware('restrictRole:judge');
            Route::get('/meeting/list',[V1\MeetingController::class,'getMeetingList'])->middleware('restrictRole:judge');
            Route::post('/meeting/detail',[V1\MeetingController::class,'getMeetingDetail'])->middleware('restrictRole:judge');
            Route::post('/meeting/edit',[V1\MeetingController::class,'editMeeting'])->middleware('restrictRole:judge');
//            Route::post('/getAppointment/edit',[V1\CourtCaseController::class,'editAppCourtAdmin'])->middleware('restrictRole:judge');
//            Route::post('/judges/list',[V1\CourtCaseController::class,'getUsersToSelect'])->middleware('restrictRole:judge');
        });
    });
    // без проверки на роль
//    Route::apiResource('/simple',V1\SimpleUsersController::class)->middleware('auth:sanctum');
});

Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::post('auth/logout',[V1\UserController::class,'logout']);
});

Route::post('/auth/login', ['as'=>'login', V1\UserController::class, 'loginUser']);
Route::post('/auth/register', [V1\UserController::class, 'createUser']);

Route::post('/download', [V1\FilesController::class,'downloadSimple']);
Route::get('/downloadG/{filename}', [V1\FilesController::class,'downloadSimpleG'])->where('filename', '[\w\s\-_\/\.]+');

//Route::get('/downloadG/{id?}', function (string $id) {
//// Check if file exists in app/storage/file folder
//    dd('123');
//    return '1';
//});

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
