<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HrEmployeeDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()?->canAccessHr()) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $documents = EmployeeDocument::query()
            ->with(['employee.department', 'uploadedBy'])
            ->when($request->filled('employee_id'), fn ($q) => $q->where('employee_id', $request->employee_id))
            ->when($request->filled('document_type'), fn ($q) => $q->where('document_type', $request->document_type))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(function ($inner) use ($s) {
                    $inner->where('title', 'like', $s)
                        ->orWhere('original_filename', 'like', $s);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('hr.documents.index', [
            'documents' => $documents,
            'employees' => Employee::where('status', 'active')->orderBy('first_name')->get(),
            'documentTypes' => config('employee_documents.types', []),
            'stats' => [
                'total' => EmployeeDocument::count(),
                'expiring' => EmployeeDocument::whereNotNull('expires_at')
                    ->whereBetween('expires_at', [now(), now()->addDays(30)])
                    ->count(),
                'employees_with_files' => EmployeeDocument::distinct('employee_id')->count('employee_id'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'document_type' => 'required|string|in:' . implode(',', array_keys(config('employee_documents.types', []))),
            'title' => 'required|string|max:255',
            'expires_at' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'file' => 'required|file|max:25600|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp,zip',
        ]);

        $file = $request->file('file');
        $path = $file->store('employee-documents/' . $validated['employee_id'], 'local');

        EmployeeDocument::create([
            'employee_id' => $validated['employee_id'],
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

        return back()->with('success', 'تم حفظ الملف في ملف الموظف.');
    }

    public function download(EmployeeDocument $employeeDocument)
    {
        if (!Storage::disk('local')->exists($employeeDocument->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            $employeeDocument->file_path,
            $employeeDocument->original_filename
        );
    }

    public function destroy(EmployeeDocument $employeeDocument)
    {
        if (Storage::disk('local')->exists($employeeDocument->file_path)) {
            Storage::disk('local')->delete($employeeDocument->file_path);
        }

        $employeeDocument->delete();

        return back()->with('success', 'تم حذف الملف.');
    }
}
