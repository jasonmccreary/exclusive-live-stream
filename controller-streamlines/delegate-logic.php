<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\CreateReportRequest;
use App\Reporter\ReportBuilder;
use Carbon\Carbon;

class CreateReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \App\Http\Requests\Report\CreateReportRequest $request
     * @param \App\Reporter\ReportBuilder $reporter
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function __invoke(CreateReportRequest $request, ReportBuilder $reporter)
    {
        if ($request->has('user')) {
            $reporter->forUser($request->get('user'));
        }

        if ($request->has('client')) {
            $reporter->forClient($request->get('client'));
        }

        if ($request->has('project')) {
            $reporter->forProject($request->get('project'));
        }

        if ($request->has('start_date') || $request->has('end_date')) {
            $reporter->forDateRange(
                Carbon::parse($request->get('start_date')),
                Carbon::parse($request->get('end_date'))
            );
        }

        $report = $reporter->generate();

        return response()->json([
            'hours' => $report->hours(),
            'users' => $report->users(),
            'projects' => $report->projects(),
            'clients' => $report->clients(),
            'entries' => $report->entries()
        ]);
    }
}
