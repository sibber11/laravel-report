<?php

namespace BlinkerBoy\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * How to make new report:
 *
 * 1. run `php artisan make:report {name}` command
 * e.g. `php artisan make:report IncomeReport`. Don't forget to add the word Report at the end
 * 2. add the name of the report to the $types array in app/Services/ReportService.php
 * 3. open the newly created report in app/Services/Reports folder
 * 4. add the query to the getReport method. Don't forget to eager load the relations
 * 5. add the columns to the $columns array
 * 6. add the format to the format method
 * 7. add the totals to the totals method
 * 8. congratulations! you have a new report
 */
class ReportController extends Controller
{
    public function index(Service $reportService)
    {
        return Inertia::render('Reports/Index', [
            'reports' => $reportService->getPermittedNames(),
        ]);
    }

    public function view($type, Service $reportService)
    {
//        if (!$this->isPermitted($reportService, $type)) {
//            abort(403);
//        }
        return Inertia::render('Reports/Fields', [
            'report' => $reportService->getType($type),
        ]);
    }

    public function show($type, Request $request, Service $reportService)
    {
//        if (!$this->isPermitted($reportService, $type)) {
//            abort(403);
//        }
        $request->validate([
            'date' => ['nullable'],
//            'action' => ['nullable', 'in:download'],
        ]);

        return $reportService->generate($type);
    }

    private function isPermitted(Service $reportService, $type): bool
    {
        return auth()->user()->can('report ' . $reportService->getType($type)->getName());
    }
}
