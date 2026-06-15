<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAdminNote;
use App\Models\EmployeeDocument;
use App\Services\Hr\EmployeeDossierService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeDossierController extends Controller
{
    public function __construct(protected EmployeeDossierService $dossier) {}

    public function show(Request $request, Employee $employee)
    {
        $this->authorizeDossier($employee);

        $periodStart = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();
        $periodEnd = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $data = $this->dossier->build($employee, $periodStart, $periodEnd);
        $viewer = Auth::user();

        if (!$viewer->canAccessHr() && !$viewer->hasRole(['super_admin', 'admin'])) {
            $data['notes'] = $data['notes']->filter(fn ($n) => !$n->is_confidential)->values();
        }

        return view('employees.dossier', array_merge($data, [
            'canManageNotes' => $this->dossier->canManageNotes($viewer),
            'canManageDocuments' => $this->dossier->canManageDocuments($viewer, $employee),
            'listQuery' => array_filter([
                'sales_only' => $request->boolean('sales_only') ? 1 : null,
                'marketing_only' => $request->boolean('marketing_only') ? 1 : null,
                'operations_only' => $request->boolean('operations_only') ? 1 : null,
            ]),
            'activeTab' => $request->get('tab', 'personal'),
        ]));
    }

    public function storeNote(Request $request, Employee $employee)
    {
        $this->authorizeDossier($employee);

        if (!$this->dossier->canManageNotes(Auth::user())) {
            abort(403);
        }

        $validated = $request->validate([
            'category' => 'required|string|in:' . implode(',', array_keys(config('employee_admin_notes.categories', []))),
            'title' => 'nullable|string|max:255',
            'body' => 'required|string|max:5000',
            'is_confidential' => 'sometimes|boolean',
        ]);

        EmployeeAdminNote::create([
            'employee_id' => $employee->id,
            'author_id' => Auth::id(),
            'category' => $validated['category'],
            'title' => $validated['title'] ?? null,
            'body' => $validated['body'],
            'is_confidential' => $request->boolean('is_confidential'),
        ]);

        return back()->with('success', 'تم حفظ الملاحظة الإدارية.');
    }

    public function destroyNote(Employee $employee, EmployeeAdminNote $employeeAdminNote)
    {
        $this->authorizeDossier($employee);

        if (!$this->dossier->canManageNotes(Auth::user()) || $employeeAdminNote->employee_id !== $employee->id) {
            abort(403);
        }

        $employeeAdminNote->delete();

        return back()->with('success', 'تم حذف الملاحظة.');
    }

    public function storeDocument(Request $request, Employee $employee)
    {
        $this->authorizeDossier($employee);

        if (!$this->dossier->canManageDocuments(Auth::user(), $employee)) {
            abort(403);
        }

        $validated = $request->validate([
            'document_type' => 'required|string|in:' . implode(',', array_keys(config('employee_documents.types', []))),
            'title' => 'required|string|max:255',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'file' => 'required|file|max:25600|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,zip',
        ]);

        $file = $request->file('file');
        $path = $file->store('employee-documents/' . $employee->id, 'local');

        EmployeeDocument::create([
            'employee_id' => $employee->id,
            'document_type' => $validated['document_type'],
            'title' => $validated['title'],
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'expires_at' => $validated['expires_at'] ?? null,
            'uploaded_by' => Auth::id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم رفع المستند بنجاح.');
    }

    public function storeCv(Request $request, Employee $employee)
    {
        $this->authorizeDossier($employee);

        if (!$this->dossier->canManageDocuments(Auth::user(), $employee)) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|max:25600|mimes:pdf,doc,docx',
        ]);

        $file = $request->file('file');
        $path = $file->store('employee-documents/' . $employee->id . '/cv', 'local');

        EmployeeDocument::create([
            'employee_id' => $employee->id,
            'document_type' => 'resume',
            'title' => 'السيرة الذاتية — ' . now()->format('Y-m-d'),
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم رفع السيرة الذاتية.');
    }

    public function downloadDocument(Employee $employee, EmployeeDocument $employeeDocument)
    {
        $this->authorizeDossier($employee);

        if ($employeeDocument->employee_id !== $employee->id) {
            abort(404);
        }

        if (!Storage::disk('local')->exists($employeeDocument->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            $employeeDocument->file_path,
            $employeeDocument->original_filename
        );
    }

    protected function authorizeDossier(Employee $employee): void
    {
        if (!$this->dossier->canView(Auth::user(), $employee)) {
            abort(403, 'لا يمكنك عرض ملف هذا الموظف.');
        }
    }
}
