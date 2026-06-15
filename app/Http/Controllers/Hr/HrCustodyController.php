<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\CustodyAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HrCustodyController extends Controller
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
        $query = CustodyAssignment::query()
            ->with(['employee.department', 'asset', 'issuedBy', 'returnedBy'])
            ->orderByDesc('issued_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', CustodyAssignment::STATUS_ACTIVE);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $assignments = $query->paginate(20)->withQueryString();

        return view('hr.custody.index', [
            'assignments' => $assignments,
            'employees' => Employee::where('status', 'active')->orderBy('first_name')->get(),
            'assets' => Asset::orderBy('name')->get(),
            'categories' => config('custody.categories', []),
            'conditions' => config('custody.conditions', []),
            'stats' => [
                'active' => CustodyAssignment::active()->count(),
                'returned_month' => CustodyAssignment::where('status', 'returned')
                    ->whereMonth('returned_at', now()->month)
                    ->count(),
                'employees_with_custody' => CustodyAssignment::active()->distinct('employee_id')->count('employee_id'),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'asset_id' => 'nullable|exists:assets,id',
            'item_name' => 'required|string|max:255',
            'category' => 'required|string|in:' . implode(',', array_keys(config('custody.categories', []))),
            'serial_number' => 'nullable|string|max:120',
            'issued_at' => 'required|date',
            'issue_condition' => 'nullable|string|in:' . implode(',', array_keys(config('custody.conditions', []))),
            'issue_notes' => 'nullable|string|max:1000',
            'issue_file' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $issueFilePath = null;
        if ($request->hasFile('issue_file')) {
            $issueFilePath = $request->file('issue_file')->store(
                'custody/issue/' . $validated['employee_id'],
                'local'
            );
        }

        $assignment = CustodyAssignment::create([
            ...$validated,
            'issued_by' => Auth::id(),
            'issue_file_path' => $issueFilePath,
            'status' => CustodyAssignment::STATUS_ACTIVE,
        ]);

        if (!empty($validated['asset_id'])) {
            Asset::where('id', $validated['asset_id'])->update([
                'assigned_to' => Employee::find($validated['employee_id'])?->user_id,
                'status' => 'assigned',
            ]);
        }

        return back()->with('success', 'تم تسجيل استلام العهدة بنجاح.');
    }

    public function returnItem(Request $request, CustodyAssignment $custodyAssignment)
    {
        if (!$custodyAssignment->isActive()) {
            return back()->with('error', 'هذه العهدة مُسلّمة مسبقاً.');
        }

        $validated = $request->validate([
            'returned_at' => 'required|date',
            'return_condition' => 'nullable|string|in:' . implode(',', array_keys(config('custody.conditions', []))),
            'return_notes' => 'nullable|string|max:1000',
            'status' => 'required|string|in:returned,lost,damaged',
            'return_file' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $returnFilePath = null;
        if ($request->hasFile('return_file')) {
            $returnFilePath = $request->file('return_file')->store(
                'custody/return/' . $custodyAssignment->employee_id,
                'local'
            );
        }

        $custodyAssignment->update([
            'returned_at' => $validated['returned_at'],
            'returned_by' => Auth::id(),
            'return_condition' => $validated['return_condition'] ?? null,
            'return_notes' => $validated['return_notes'] ?? null,
            'return_file_path' => $returnFilePath,
            'status' => $validated['status'],
        ]);

        if ($custodyAssignment->asset_id) {
            Asset::where('id', $custodyAssignment->asset_id)->update([
                'assigned_to' => null,
                'status' => 'active',
            ]);
        }

        return back()->with('success', 'تم تسجيل تسليم العهدة.');
    }

    public function downloadIssueFile(CustodyAssignment $custodyAssignment)
    {
        return $this->downloadFile($custodyAssignment->issue_file_path, 'issue-receipt');
    }

    public function downloadReturnFile(CustodyAssignment $custodyAssignment)
    {
        return $this->downloadFile($custodyAssignment->return_file_path, 'return-receipt');
    }

    protected function downloadFile(?string $path, string $fallback)
    {
        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return Storage::disk('local')->download($path, $fallback);
    }
}
