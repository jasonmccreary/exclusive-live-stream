<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleStore;

class ScheduleController extends Controller
{
    public function store(ScheduleStore $request)
    {
        return response()->json([
            'success' => Schedule::createFromRequest($request)
        ]);
    }
}


class Schedule extends Model
{
    public static function createFromRequest(ScheduleStore $request) {
        $request->validated();
    }
}
