<?php

namespace App\Http\Controllers;

use App\Models\FinanceAiReport;
use Illuminate\Http\Request;

class FinanceAiReportController extends Controller
{
    public function show(Request $request, FinanceAiReport $report)
    {
        abort_unless($report->user_id === $request->user()->id, 404);
        return response()->view('finance-ai.report', [
            'report'=>$report,
            'data'=>$report->snapshot,
        ])->header('Cache-Control','private, no-store');
    }
}
