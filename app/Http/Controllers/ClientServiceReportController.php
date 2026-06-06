<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientServiceReport;
use App\Services\ClientPortalNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientServiceReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientServiceReport::with(['client', 'uploader'])->latest();

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->integer('client_id'));
        }

        $reports = $query->paginate(20)->withQueryString();

        return view('client-service-reports.index', compact('reports'));
    }

    public function create()
    {
        return view('client-service-reports.create');
    }

    /**
     * رفع تقرير من لوحة الإدارة (اختيار العميل من القائمة).
     */
    public function storeFromAdmin(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt',
        ]);

        $client = Client::findOrFail((int) $request->input('client_id'));
        $this->persistReport($client, $request);

        return redirect()
            ->route('client-service-reports.index')
            ->with('success', 'تم رفع التقرير للعميل بنجاح');
    }

    /**
     * رفع تقرير من صفحة تفاصيل العميل.
     */
    public function store(Request $request, Client $client)
    {
        $this->persistReport($client, $request);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'تم رفع التقرير بنجاح');
    }

    public function destroyStandalone(ClientServiceReport $serviceReport)
    {
        $this->removeStoredFile($serviceReport);
        $serviceReport->delete();

        return redirect()
            ->route('client-service-reports.index')
            ->with('success', 'تم حذف التقرير');
    }

    public function destroy(Client $client, ClientServiceReport $serviceReport)
    {
        if ($serviceReport->client_id !== $client->id) {
            abort(404);
        }

        $this->removeStoredFile($serviceReport);
        $serviceReport->delete();

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'تم حذف التقرير');
    }

    public function download(Request $request, Client $client, ClientServiceReport $serviceReport)
    {
        if ($serviceReport->client_id !== $client->id) {
            abort(404);
        }

        if (! Storage::disk('local')->exists($serviceReport->file_path)) {
            abort(404, 'الملف غير موجود');
        }

        return Storage::disk('local')->download(
            $serviceReport->file_path,
            $serviceReport->original_filename ?? basename($serviceReport->file_path)
        );
    }

    private function persistReport(Client $client, Request $request): ClientServiceReport
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt',
        ]);

        $file = $request->file('file');
        $path = $file->store('client-service-reports/'.$client->id, 'local');

        $report = ClientServiceReport::create([
            'client_id' => $client->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'uploaded_by' => $request->user()?->id,
        ]);

        ClientPortalNotifier::notify(
            (int) $client->id,
            'service_report_uploaded',
            'تقرير خدمة جديد',
            'تم رفع تقرير: '.$report->title,
            url('/client/service-reports'),
            ['report_id' => $report->id]
        );

        return $report;
    }

    private function removeStoredFile(ClientServiceReport $serviceReport): void
    {
        if (Storage::disk('local')->exists($serviceReport->file_path)) {
            Storage::disk('local')->delete($serviceReport->file_path);
        }
    }
}
