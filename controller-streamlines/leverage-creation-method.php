<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleStore;

class ScheduleController extends Controller
{
    public function store(ScheduleStore $request)
    {
        $attributes = $request->validated();

        $schedule = $request->validated();
        unset($schedule['scheduleServiceTasks']);

        $createdSchedule = Schedule::create($schedule);

        foreach ($attributes['scheduleServiceTasks'] as $scheduleServiceTask) {
            $createdSchedule->servicetasks()->create([
                'task' => $scheduleServiceTask['task']
            ]);
        }

        return response()->json(['success' => true]);
    }
}
