<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Reports\ExcelReportExporter;
use App\Services\Reports\SystemReportCatalog;
use App\Services\Reports\SystemReportDataService;
use Illuminate\Http\Request;

class AdminSystemReportController extends Controller
{
    public function __construct(
        protected SystemReportDataService $dataService,
        protected ExcelReportExporter $exporter,
    ) {}

    public function index()
    {
        return view('admin.system-reports.index', [
            'grouped' => SystemReportCatalog::groupedByCategory(),
            'categories' => SystemReportCatalog::categories(),
        ]);
    }

    public function show(string $report, Request $request)
    {
        if (!SystemReportCatalog::exists($report)) {
            abort(404);
        }

        $meta = SystemReportCatalog::get($report);
        $payload = $this->dataService->build($report, $request);

        return view('admin.system-reports.show', [
            'meta' => $meta,
            'payload' => $payload,
            'supportsDateFilter' => $meta['supports_date_filter'] ?? false,
        ]);
    }

    public function export(string $report, Request $request)
    {
        if (!SystemReportCatalog::exists($report)) {
            abort(404);
        }

        $meta = SystemReportCatalog::get($report);
        $payload = $this->dataService->build($report, $request);
        $filename = ($meta['title'] ?? $report) . '_' . now()->format('Y-m-d');

        return $this->exporter->download($payload, $filename);
    }
}
