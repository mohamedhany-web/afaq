<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HrEmployeeContractController extends Controller
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
        $contracts = EmployeeContract::query()
            ->with(['employee.department', 'createdBy'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when($request->filled('contract_type'), fn ($q) => $q->where('contract_type', $request->contract_type))
            ->when($request->filled('employee_id'), fn ($q) => $q->where('employee_id', $request->employee_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(function ($inner) use ($s) {
                    $inner->where('title', 'like', $s)
                        ->orWhere('contract_number', 'like', $s);
                });
            })
            ->orderByDesc('start_date')
            ->paginate(20)
            ->withQueryString();

        return view('hr.contracts.index', [
            'contracts' => $contracts,
            'employees' => Employee::where('status', 'active')->orderBy('first_name')->get(),
            'contractTypes' => config('hr_contracts.types', []),
            'stats' => [
                'active' => EmployeeContract::active()->count(),
                'expiring' => EmployeeContract::active()
                    ->whereNotNull('end_date')
                    ->whereBetween('end_date', [now(), now()->addDays(30)])
                    ->count(),
                'draft' => EmployeeContract::where('status', 'draft')->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'title' => 'required|string|max:255',
            'contract_type' => 'required|string|in:' . implode(',', array_keys(config('hr_contracts.types', []))),
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:' . implode(',', array_keys(config('hr_contracts.status_labels', []))),
            'terms' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:25600|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        $filePath = null;
        $originalName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('employee-contracts/' . $validated['employee_id'], 'local');
            $originalName = $file->getClientOriginalName();
        }

        EmployeeContract::create([
            ...$validated,
            'contract_number' => EmployeeContract::generateNumber(),
            'file_path' => $filePath,
            'original_filename' => $originalName,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم إنشاء العقد بنجاح.');
    }

    public function update(Request $request, EmployeeContract $employeeContract)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'contract_type' => 'required|string|in:' . implode(',', array_keys(config('hr_contracts.types', []))),
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:' . implode(',', array_keys(config('hr_contracts.status_labels', []))),
            'terms' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:1000',
            'file' => 'nullable|file|max:25600|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        if ($request->hasFile('file')) {
            if ($employeeContract->file_path && Storage::disk('local')->exists($employeeContract->file_path)) {
                Storage::disk('local')->delete($employeeContract->file_path);
            }

            $file = $request->file('file');
            $validated['file_path'] = $file->store('employee-contracts/' . $employeeContract->employee_id, 'local');
            $validated['original_filename'] = $file->getClientOriginalName();
        }

        $employeeContract->update($validated);

        return back()->with('success', 'تم تحديث العقد.');
    }

    public function download(EmployeeContract $employeeContract)
    {
        if (!$employeeContract->file_path || !Storage::disk('local')->exists($employeeContract->file_path)) {
            abort(404);
        }

        return Storage::disk('local')->download(
            $employeeContract->file_path,
            $employeeContract->original_filename ?? 'contract.pdf'
        );
    }

    public function destroy(EmployeeContract $employeeContract)
    {
        if ($employeeContract->file_path && Storage::disk('local')->exists($employeeContract->file_path)) {
            Storage::disk('local')->delete($employeeContract->file_path);
        }

        $employeeContract->delete();

        return back()->with('success', 'تم حذف العقد.');
    }
}
