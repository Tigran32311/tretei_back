<?php

namespace App\Http\Controllers\Api\V1;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Models\SimpleUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SimpleUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SimpleUsers::all();
    }

    public function getUsersToSelect()
    {
        $simple = SimpleUsers::all();
        $user = auth('sanctum')->id();

        if ($simple->count()>1) {
            $simple->each(function (SimpleUsers $item, int $key) {
                $user = auth('sanctum')->id();
                if ($item->getAttribute('user_id') == $user) {
                    $item->setAttribute('fio', 'Вы');
                } else {
//                $plaintif = User::all()->where([['id',$item->getAttribute('user_id')],['role','==','simpleUser']]);
                    $plaintif = User::all()->where('id', $item->getAttribute('user_id'))->where('role', 'simpleUser');
                    if ($plaintif->count() > 0) {
                        $item->setAttribute('fio', $plaintif->value('last_name') . ' ' . mb_substr($plaintif->value('name'), 0, 1) . '.' . mb_substr($plaintif->value('second_name'), 0, 1) . '.');
                    } else {
                        $item->delete();
                    }
                }
            });
        } else {
            $user = auth('sanctum')->id();
            if ($simple->value('user_id') == $user) {
                $simple->put('fio', 'Вы');
            } else {
//                $plaintif = User::all()->where([['id',$item->getAttribute('user_id')],['role','==','simpleUser']]);
                $plaintif = User::all()->where('id', $simple->value('user_id'))->where('role', 'simpleUser');
                if ($plaintif->count() > 0) {
                    $simple->put('fio', $plaintif->value('last_name') . ' ' . mb_substr($plaintif->value('name'), 0, 1) . '.' . mb_substr($plaintif->value('second_name'), 0, 1) . '.');
                } else {
                    return null;
                }
            }
        }
        return $simple;
    }

    public function getById(Request $request)
    {
        $user =  auth('sanctum')->user();
//        $user = User::where('id', $request->email)->first();
        return $user;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
