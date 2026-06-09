<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPenaltyLog;
use App\Models\AutoPenaltyRule;
use App\Services\AutoPenaltyService;
use Illuminate\Http\Request;

class AutoPenaltyController extends Controller
{
    public function index(AutoPenaltyService $penalties)
    {
        $rules = AutoPenaltyRule::query()
            ->withCount('logs')
            ->orderBy('department_code')
            ->orderBy('source_type')
            ->get();
        $recentLogs = AutoPenaltyLog::query()
            ->with(['user:id,name', 'rule:id,name,department_code'])
            ->latest('applied_at')
            ->limit(50)
            ->get();

        $stats = [
            'rules_active' => $rules->where('is_active', true)->count(),
            'applied_today' => AutoPenaltyLog::whereDate('applied_at', today())->count(),
            'applied_month_amount' => AutoPenaltyLog::query()
                ->whereMonth('applied_at', now()->month)
                ->whereYear('applied_at', now()->year)
                ->sum('amount'),
            'total_logs' => AutoPenaltyLog::count(),
        ];

        return view('admin.auto-penalties.index', [
            'rules' => $rules,
            'recentLogs' => $recentLogs,
            'departmentSummary' => $penalties->departmentSummary(),
            'stats' => $stats,
            'departments' => config('auto_penalties.departments', []),
            'sourceTypes' => config('auto_penalties.source_types', []),
            'appliesTo' => config('auto_penalties.applies_to', []),
            'reportPeriodTypes' => config('auto_penalties.report_period_types', []),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateRule($request);
        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $request->boolean('is_active', true);

        AutoPenaltyRule::create($data);

        return back()->with('success', 'تم إضافة قاعدة العقوبة بنجاح.');
    }

    public function update(Request $request, AutoPenaltyRule $autoPenaltyRule)
    {
        $data = $this->validateRule($request);
        $data['is_active'] = $request->boolean('is_active');

        $autoPenaltyRule->update($data);

        return back()->with('success', 'تم تحديث قاعدة العقوبة.');
    }

    public function toggle(AutoPenaltyRule $autoPenaltyRule)
    {
        $autoPenaltyRule->update(['is_active' => !$autoPenaltyRule->is_active]);

        return back()->with('success', $autoPenaltyRule->is_active ? 'تم تفعيل القاعدة.' : 'تم إيقاف القاعدة.');
    }

    public function destroy(AutoPenaltyRule $autoPenaltyRule)
    {
        if ($autoPenaltyRule->logs()->exists()) {
            return back()->with('error', 'لا يمكن حذف قاعدة مرتبطة بخصومات مطبّقة. أوقفها بدلاً من ذلك.');
        }

        $autoPenaltyRule->delete();

        return back()->with('success', 'تم حذف القاعدة.');
    }

    public function processNow(AutoPenaltyService $penalties)
    {
        $stats = $penalties->processAll();

        return back()->with('success', sprintf(
            'تم التطبيق: %d خصم | تم التخطي: %d | أخطاء: %d',
            $stats['applied'],
            $stats['skipped'],
            $stats['errors'],
        ));
    }

    protected function validateRule(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'department_code' => ['nullable', 'string', 'max:16'],
            'source_type' => ['required', 'string', 'in:' . implode(',', array_keys(config('auto_penalties.source_types', [])))],
            'report_period_type' => ['nullable', 'string', 'in:' . implode(',', array_keys(config('auto_penalties.report_period_types', [])))],
            'amount' => ['required', 'numeric', 'min:0'],
            'applies_to' => ['required', 'string', 'in:' . implode(',', array_keys(config('auto_penalties.applies_to', [])))],
            'grace_hours' => ['required', 'integer', 'min:0', 'max:720'],
        ], [], [
            'name' => 'اسم القاعدة',
            'department_code' => 'القسم',
            'source_type' => 'نوع المخالفة',
            'amount' => 'مبلغ الخصم',
            'grace_hours' => 'ساعات السماح',
        ]);
    }
}
