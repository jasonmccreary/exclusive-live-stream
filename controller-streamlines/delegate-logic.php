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
     * @param \App\Reporter\ReportBuilder $reportBuilder
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function __invoke(CreateReportRequest $request, ReportBuilder $reportBuilder)
    {
        $report = $reportBuilder->reportFromRequest($request);

        return response()->json([
            'hours' => $report->hours(),
            'users' => $report->users(),
            'projects' => $report->projects(),
            'clients' => $report->clients(),
            'entries' => $report->entries()
        ]);
    }
}
