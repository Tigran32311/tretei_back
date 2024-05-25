<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SimpleUsers;
use App\Models\AppointmentStatuses;
use App\Models\Appointment;
use App\Models\DocsAppointments;
use App\Models\StatusAppointments;
use App\Models\ThirdParties;
use App\Models\User;
use Bitrix\Im\App;
use Bitrix\Tasks\Scrum\Controllers\DoD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Response;

class FilesController extends Controller
{
    public function downloadSimple(Request $request) {
        $filename = $request->input('filename');
//        $file_path = storage_path().'/'.$filename;
        $file_path = URL::to('/').'/'.$filename;

//        $file = Storage::disk('local')->get($filename);
//        $file = Storage::disk('local')->exists($filename);
//        dd($file);

        if (Storage::disk('local')->exists($filename))
        {
            $file = Storage::disk('local')->get($filename);
//        // Send Download
            return (new Response($file, 200))
              ->header('Content-Type', '');
//            return Response::download($file_path, $filename, [
//                'Content-Length: '. filesize($file_path)
//            ]);
        }
        else
        {
            // Error
        exit('Requested file does not exist on our server!');
        }
    }

    public function downloadSimpleG(string $filename) {
//        dd($filename);
        if (Storage::disk('local')->exists($filename))
        {
            $file = Storage::disk('local')->get($filename);
//            $file = Storage::disk('local')->($filename);
            $file = Storage::disk('local')->url($filename);
            $file = Storage::download($filename);
            return $file;
//            return (new Response($file, 200))
//                ->header('Content-Type', '');
        }
        else
        {
            // Error
            exit('Requested file does not exist on our server!');
        }
    }
}
