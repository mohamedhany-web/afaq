<?php

namespace App\Services\Reports;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\Compensation\CompPayrollRun;
use App\Models\CrmFollowUp;
use App\Models\CrmTask;
use App\Models\DailySalesReport;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Salary;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SystemReportDataService
{
    public function build(string $key, Request $request): array
    {
        $meta = SystemReportCatalog::get($key);
        $filters = $this->resolveFilters($request, $meta['supports_date_filter'] ?? false);

        $method = 'report' . str($key)->studly()->toString();

        if (!method_exists($this, $method)) {
            abort(404);
        }

        $payload = $this->{$method}($filters);
        $payload['title'] = $meta['title'] ?? $key;
        $payload['sheet_title'] = mb_substr($payload['title'], 0, 31);
        $payload['generated_at'] = now()->format('Y-m-d H:i');
        $payload['period_label'] = $filters['period_label'];
        $payload['report_key'] = $key;
        $payload['filters'] = $filters;

        return $payload;
    }

    /** @return array{start_date:string,end_date:string,period_label:string} */
    protected function resolveFilters(Request $request, bool $supportsDate): array
    {
        $end = $request->get('end_date', now()->format('Y-m-d'));
        $start = $request->get('start_date', Carbon::parse($end)->subDays(30)->format('Y-m-d'));

        if (!$supportsDate) {
            return [
                'start_date' => null,
                'end_date' => null,
                'period_label' => 'كامل النظام',
            ];
        }

        return [
            'start_date' => $start,
            'end_date' => $end,
            'period_label' => $start . ' → ' . $end,
        ];
    }

    protected function stageLabel(string $stage, string $map = 'lead_stage_labels'): string
    {
        return config("system_reports.{$map}.{$stage}", $stage);
    }

    protected function reportExecutiveSummary(array $filters): array
    {
        $clients = Client::count();
        $salesOpen = Sale::whereNotIn('stage', ['closed_won', 'closed_lost'])->count();
        $salesWon = Sale::where('stage', 'closed_won')->count();
        $pipelineValue = (float) Sale::whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('estimated_value');
        $wonValue = (float) Sale::where('stage', 'closed_won')->sum('estimated_value');
        $projects = Project::count();
        $activeProjects = Project::whereIn('listing_status', ['upcoming', 'active'])->count();
        $employees = Employee::where('status', 'active')->count();
        $tasksOpen = CrmTask::whereNotIn('status', ['completed', 'verified', 'archived', 'cancelled'])->count();
        $tasksOverdue = CrmTask::where('status', 'overdue')->count();
        $followUpsPending = CrmFollowUp::where('status', 'scheduled')->count();
        $teams = SalesTeam::where('is_active', true)->count();

        return [
            'summary' => [
                ['label' => 'إجمالي العملاء', 'value' => $clients],
                ['label' => 'صفقات مفتوحة', 'value' => $salesOpen],
                ['label' => 'صفقات مكسوبة', 'value' => $salesWon],
                ['label' => 'قيمة المسار (مفتوح)', 'value' => number_format($pipelineValue, 0) . ' ج.م'],
                ['label' => 'قيمة المبيعات المغلقة', 'value' => number_format($wonValue, 0) . ' ج.م'],
                ['label' => 'المشاريع العقارية', 'value' => $projects . ' (' . $activeProjects . ' نشط)'],
                ['label' => 'الموظفون النشطون', 'value' => $employees],
                ['label' => 'مهام CRM مفتوحة', 'value' => $tasksOpen],
                ['label' => 'مهام متأخرة', 'value' => $tasksOverdue],
                ['label' => 'متابعات مجدولة', 'value' => $followUpsPending],
                ['label' => 'فرق مبيعات نشطة', 'value' => $teams],
            ],
            'columns' => [
                ['key' => 'metric', 'label' => 'المؤشر'],
                ['key' => 'value', 'label' => 'القيمة'],
                ['key' => 'notes', 'label' => 'ملاحظة'],
            ],
            'rows' => [
                ['metric' => 'عملاء', 'value' => $clients, 'notes' => 'إجمالي قاعدة العملاء'],
                ['metric' => 'مسار المبيعات', 'value' => number_format($pipelineValue, 0), 'notes' => $salesOpen . ' صفقة مفتوحة'],
                ['metric' => 'مبيعات مغلقة', 'value' => number_format($wonValue, 0), 'notes' => $salesWon . ' صفقة'],
                ['metric' => 'مشاريع', 'value' => $projects, 'notes' => $activeProjects . ' مشروع نشط/قريب'],
                ['metric' => 'موظفون', 'value' => $employees, 'notes' => 'حالة نشط'],
                ['metric' => 'مهام CRM', 'value' => $tasksOpen, 'notes' => $tasksOverdue . ' متأخرة'],
                ['metric' => 'متابعات', 'value' => $followUpsPending, 'notes' => 'مجدولة'],
                ['metric' => 'فرق', 'value' => $teams, 'notes' => 'فرق مبيعات نشطة'],
            ],
        ];
    }

    protected function reportCrmClients(array $filters): array
    {
        $query = Client::with(['assignedEmployee.user', 'createdBy'])
            ->orderByDesc('updated_at');

        if ($filters['start_date']) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        $clients = $query->get();

        return [
            'summary' => [
                ['label' => 'عدد العملاء', 'value' => $clients->count()],
                ['label' => 'عملاء جدد (مرحلة lead)', 'value' => $clients->where('lead_stage', 'lead')->count()],
                ['label' => 'صفقات مغلقة (فوز)', 'value' => $clients->where('lead_stage', 'closed_won')->count()],
            ],
            'columns' => [
                ['key' => 'name', 'label' => 'الاسم'],
                ['key' => 'phone', 'label' => 'الهاتف'],
                ['key' => 'email', 'label' => 'البريد'],
                ['key' => 'status', 'label' => 'الحالة'],
                ['key' => 'lead_stage', 'label' => 'مرحلة الرحلة'],
                ['key' => 'assigned', 'label' => 'المسؤول'],
                ['key' => 'created_at', 'label' => 'تاريخ الإنشاء'],
            ],
            'rows' => $clients->map(fn (Client $c) => [
                'name' => $c->name,
                'phone' => $c->phone ?? '—',
                'email' => $c->email ?? '—',
                'status' => $c->status ?? '—',
                'lead_stage' => $this->stageLabel($c->lead_stage ?? 'lead'),
                'assigned' => $c->assignedEmployee?->user?->name ?? '—',
                'created_at' => $c->created_at?->format('Y-m-d'),
            ])->all(),
        ];
    }

    protected function reportSalesPipeline(array $filters): array
    {
        $query = Sale::with(['client', 'salesRep', 'project', 'salesTeam'])
            ->orderByDesc('updated_at');

        if ($filters['start_date']) {
            $query->where(function ($q) use ($filters) {
                $q->whereBetween('expected_close_date', [$filters['start_date'], $filters['end_date']])
                    ->orWhereBetween('created_at', [
                        Carbon::parse($filters['start_date'])->startOfDay(),
                        Carbon::parse($filters['end_date'])->endOfDay(),
                    ]);
            });
        }

        $sales = $query->get();
        $totalValue = $sales->sum('estimated_value');

        return [
            'summary' => [
                ['label' => 'عدد الصفقات', 'value' => $sales->count()],
                ['label' => 'إجمالي القيمة المتوقعة', 'value' => number_format((float) $totalValue, 0) . ' ج.م'],
                ['label' => 'متوسط الصفقة', 'value' => $sales->count() ? number_format($totalValue / $sales->count(), 0) . ' ج.م' : '0'],
            ],
            'columns' => [
                ['key' => 'client', 'label' => 'العميل'],
                ['key' => 'stage', 'label' => 'المرحلة'],
                ['key' => 'value', 'label' => 'القيمة المتوقعة', 'type' => 'money'],
                ['key' => 'probability', 'label' => 'الاحتمال %', 'type' => 'percent'],
                ['key' => 'assignee', 'label' => 'المندوب'],
                ['key' => 'team', 'label' => 'الفريق'],
                ['key' => 'close_date', 'label' => 'تاريخ الإغلاق المتوقع'],
                ['key' => 'project', 'label' => 'المشروع'],
            ],
            'rows' => $sales->map(fn (Sale $s) => [
                'client' => $s->client?->name ?? '—',
                'stage' => $this->stageLabel($s->stage ?? 'lead', 'sale_stage_labels'),
                'value' => $s->estimated_value,
                'probability' => $s->probability_percentage,
                'assignee' => $s->salesRep?->name ?? '—',
                'team' => $s->salesTeam?->name ?? '—',
                'close_date' => $s->expected_close_date?->format('Y-m-d') ?? '—',
                'project' => $s->project?->name ?? '—',
            ])->all(),
        ];
    }

    protected function reportCrmTasks(array $filters): array
    {
        $query = CrmTask::with(['assignee', 'client', 'project'])
            ->orderByDesc('created_at');

        if ($filters['start_date']) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        $tasks = $query->get();
        $statusLabels = config('crm_tasks.status_labels', []);

        return [
            'summary' => [
                ['label' => 'إجمالي المهام', 'value' => $tasks->count()],
                ['label' => 'مكتملة / موثقة', 'value' => $tasks->whereIn('status', ['completed', 'verified'])->count()],
                ['label' => 'متأخرة', 'value' => $tasks->where('status', 'overdue')->count()],
                ['label' => 'متوسط درجة الأداء', 'value' => round((float) $tasks->whereNotNull('performance_score')->avg('performance_score'), 1)],
            ],
            'columns' => [
                ['key' => 'title', 'label' => 'المهمة'],
                ['key' => 'status', 'label' => 'الحالة'],
                ['key' => 'priority', 'label' => 'الأولوية'],
                ['key' => 'assignee', 'label' => 'المكلف'],
                ['key' => 'client', 'label' => 'العميل'],
                ['key' => 'due_at', 'label' => 'الاستحقاق'],
                ['key' => 'score', 'label' => 'الأداء'],
                ['key' => 'category', 'label' => 'التصنيف'],
            ],
            'rows' => $tasks->map(fn (CrmTask $t) => [
                'title' => $t->title,
                'status' => $statusLabels[$t->status] ?? $t->status,
                'priority' => $t->priority,
                'assignee' => $t->assignee?->name ?? '—',
                'client' => $t->client?->name ?? '—',
                'due_at' => $t->due_at?->format('Y-m-d H:i') ?? '—',
                'score' => $t->performance_score ?? '—',
                'category' => $t->category ?? '—',
            ])->all(),
        ];
    }

    protected function reportDailySalesReports(array $filters): array
    {
        $query = DailySalesReport::with('author')->orderByDesc('report_date');

        if ($filters['start_date']) {
            $query->whereBetween('report_date', [$filters['start_date'], $filters['end_date']]);
        }

        $reports = $query->get();

        return [
            'summary' => [
                ['label' => 'عدد التقارير', 'value' => $reports->count()],
                ['label' => 'مُرسلة', 'value' => $reports->where('status', DailySalesReport::STATUS_SUBMITTED)->count()],
                ['label' => 'مسودة', 'value' => $reports->where('status', DailySalesReport::STATUS_DRAFT)->count()],
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'التاريخ'],
                ['key' => 'author', 'label' => 'المندوب'],
                ['key' => 'status', 'label' => 'الحالة'],
                ['key' => 'calls', 'label' => 'مكالمات اليوم'],
                ['key' => 'meetings', 'label' => 'اجتماعات'],
                ['key' => 'submitted_at', 'label' => 'وقت الإرسال'],
            ],
            'rows' => $reports->map(fn (DailySalesReport $r) => [
                'date' => $r->report_date?->format('Y-m-d'),
                'author' => $r->author?->name ?? '—',
                'status' => $r->status === DailySalesReport::STATUS_SUBMITTED ? 'مُرسل' : 'مسودة',
                'calls' => $r->metric('activity', 'calls_made', 0),
                'meetings' => $r->metric('activity', 'meetings_held', 0),
                'submitted_at' => $r->submitted_at?->format('Y-m-d H:i') ?? '—',
            ])->all(),
        ];
    }

    protected function reportFollowUps(array $filters): array
    {
        $query = CrmFollowUp::with(['user', 'client', 'sale'])->orderByDesc('scheduled_at');

        if ($filters['start_date']) {
            $query->whereBetween('scheduled_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        $items = $query->get();
        $statusMap = [
            'scheduled' => 'مجدولة',
            'completed' => 'منجزة',
            'cancelled' => 'ملغاة',
        ];

        return [
            'summary' => [
                ['label' => 'إجمالي المتابعات', 'value' => $items->count()],
                ['label' => 'مجدولة', 'value' => $items->where('status', 'scheduled')->count()],
                ['label' => 'منجزة', 'value' => $items->where('status', 'completed')->count()],
            ],
            'columns' => [
                ['key' => 'type', 'label' => 'النوع'],
                ['key' => 'client', 'label' => 'العميل'],
                ['key' => 'user', 'label' => 'المسؤول'],
                ['key' => 'scheduled_at', 'label' => 'الموعد'],
                ['key' => 'status', 'label' => 'الحالة'],
                ['key' => 'notes', 'label' => 'ملاحظات'],
            ],
            'rows' => $items->map(fn (CrmFollowUp $f) => [
                'type' => CrmFollowUp::TYPE_LABELS[$f->interaction_type] ?? $f->interaction_type,
                'client' => $f->client?->name ?? '—',
                'user' => $f->user?->name ?? '—',
                'scheduled_at' => $f->scheduled_at?->format('Y-m-d H:i') ?? '—',
                'status' => $statusMap[$f->status] ?? $f->status,
                'notes' => mb_substr($f->notes ?? '', 0, 120),
            ])->all(),
        ];
    }

    protected function reportSalesTeams(array $filters): array
    {
        $teams = SalesTeam::with(['manager', 'department'])
            ->withCount('members')
            ->orderBy('name')
            ->get();

        return [
            'summary' => [
                ['label' => 'عدد الفرق', 'value' => $teams->count()],
                ['label' => 'فرق نشطة', 'value' => $teams->where('is_active', true)->count()],
                ['label' => 'إجمالي الأعضاء', 'value' => $teams->sum('members_count')],
            ],
            'columns' => [
                ['key' => 'name', 'label' => 'اسم الفريق'],
                ['key' => 'manager', 'label' => 'المدير'],
                ['key' => 'members', 'label' => 'الأعضاء', 'type' => 'number'],
                ['key' => 'department', 'label' => 'القسم'],
                ['key' => 'active', 'label' => 'نشط'],
            ],
            'rows' => $teams->map(fn (SalesTeam $t) => [
                'name' => $t->name,
                'manager' => $t->manager?->name ?? '—',
                'members' => $t->members_count,
                'department' => $t->department?->name ?? '—',
                'active' => $t->is_active ? 'نعم' : 'لا',
            ])->all(),
        ];
    }

    protected function reportCompensation(array $filters): array
    {
        if (!Schema::hasTable('comp_payroll_runs')) {
            return [
                'summary' => [['label' => 'ملاحظة', 'value' => 'وحدة التعويضات غير مفعّلة بعد']],
                'columns' => [['key' => 'message', 'label' => 'رسالة']],
                'rows' => [['message' => 'قم بتشغيل migrations التعويضات أولاً']],
            ];
        }

        $query = CompPayrollRun::with(['period', 'user'])->orderByDesc('id');

        if ($filters['start_date'] && Schema::hasColumn('comp_payroll_runs', 'created_at')) {
            $query->whereBetween('created_at', [
                Carbon::parse($filters['start_date'])->startOfDay(),
                Carbon::parse($filters['end_date'])->endOfDay(),
            ]);
        }

        $runs = $query->limit(500)->get();

        return [
            'summary' => [
                ['label' => 'دورات رواتب', 'value' => $runs->count()],
                ['label' => 'معتمدة', 'value' => $runs->where('status', 'approved')->count()],
                ['label' => 'مسودة', 'value' => $runs->where('status', 'draft')->count()],
            ],
            'columns' => [
                ['key' => 'employee', 'label' => 'الموظف'],
                ['key' => 'period', 'label' => 'الفترة'],
                ['key' => 'status', 'label' => 'الحالة'],
                ['key' => 'net_pay', 'label' => 'صافي الراتب', 'type' => 'money'],
                ['key' => 'kpi_score', 'label' => 'درجة KPI'],
                ['key' => 'commission', 'label' => 'عمولات', 'type' => 'money'],
                ['key' => 'calculated_at', 'label' => 'تاريخ الحساب'],
            ],
            'rows' => $runs->map(fn (CompPayrollRun $r) => [
                'employee' => $r->user?->name ?? '—',
                'period' => $r->period ? $r->period->label : '—',
                'status' => $r->status,
                'net_pay' => $r->net_pay,
                'kpi_score' => $r->kpi_score,
                'commission' => $r->commission_total,
                'calculated_at' => $r->calculated_at?->format('Y-m-d H:i') ?? '—',
            ])->all(),
        ];
    }

    protected function reportRealEstateProjects(array $filters): array
    {
        $projects = Project::orderBy('name')->get();
        $statusLabels = Project::LISTING_STATUSES;

        return [
            'summary' => [
                ['label' => 'إجمالي المشاريع', 'value' => $projects->count()],
                ['label' => 'متاح للبيع', 'value' => $projects->where('listing_status', 'active')->count()],
                ['label' => 'إجمالي الوحدات', 'value' => $projects->sum('total_units')],
                ['label' => 'وحدات مباعة', 'value' => $projects->sum('sold_units')],
            ],
            'columns' => [
                ['key' => 'name', 'label' => 'المشروع'],
                ['key' => 'city', 'label' => 'المدينة'],
                ['key' => 'listing_status', 'label' => 'حالة العرض'],
                ['key' => 'total_units', 'label' => 'إجمالي الوحدات', 'type' => 'number'],
                ['key' => 'available', 'label' => 'متاح', 'type' => 'number'],
                ['key' => 'sold', 'label' => 'مباع', 'type' => 'number'],
                ['key' => 'price_from', 'label' => 'من سعر', 'type' => 'money'],
                ['key' => 'price_to', 'label' => 'إلى سعر', 'type' => 'money'],
            ],
            'rows' => $projects->map(fn (Project $p) => [
                'name' => $p->name,
                'city' => $p->city ?? '—',
                'listing_status' => $statusLabels[$p->listing_status] ?? ($p->listing_status ?? '—'),
                'total_units' => $p->total_units,
                'available' => $p->available_units,
                'sold' => $p->sold_units,
                'price_from' => $p->price_from,
                'price_to' => $p->price_to,
            ])->all(),
        ];
    }

    protected function reportEmployees(array $filters): array
    {
        $employees = Employee::with(['department', 'user'])
            ->orderBy('first_name')
            ->get();

        $statusMap = ['active' => 'نشط', 'inactive' => 'غير نشط', 'terminated' => 'منتهي'];

        return [
            'summary' => [
                ['label' => 'إجمالي الموظفين', 'value' => $employees->count()],
                ['label' => 'نشطون', 'value' => $employees->where('status', 'active')->count()],
                ['label' => 'إجمالي الرواتب الأساسية', 'value' => number_format((float) $employees->sum('salary'), 0) . ' ج.م'],
            ],
            'columns' => [
                ['key' => 'name', 'label' => 'الاسم'],
                ['key' => 'department', 'label' => 'القسم'],
                ['key' => 'position', 'label' => 'المسمى'],
                ['key' => 'status', 'label' => 'الحالة'],
                ['key' => 'salary', 'label' => 'الراتب الأساسي', 'type' => 'money'],
                ['key' => 'hire_date', 'label' => 'تاريخ التعيين'],
                ['key' => 'email', 'label' => 'البريد'],
            ],
            'rows' => $employees->map(fn (Employee $e) => [
                'name' => trim($e->first_name . ' ' . $e->last_name),
                'department' => $e->department?->name ?? '—',
                'position' => $e->position ?? '—',
                'status' => $statusMap[$e->status] ?? $e->status,
                'salary' => $e->salary,
                'hire_date' => $e->hire_date?->format('Y-m-d') ?? '—',
                'email' => $e->user?->email ?? $e->email ?? '—',
            ])->all(),
        ];
    }

    protected function reportAttendance(array $filters): array
    {
        $query = Attendance::with('employee')->orderByDesc('date');

        if ($filters['start_date']) {
            $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
        }

        $records = $query->limit(2000)->get();
        $present = $records->where('status', 'present')->count();
        $rate = $records->count() ? round($present / $records->count() * 100, 1) : 0;

        $statusMap = ['present' => 'حاضر', 'absent' => 'غائب', 'late' => 'متأخر', 'leave' => 'إجازة'];

        return [
            'summary' => [
                ['label' => 'سجلات الحضور', 'value' => $records->count()],
                ['label' => 'أيام حضور', 'value' => $present],
                ['label' => 'نسبة الحضور', 'value' => $rate . '%'],
                ['label' => 'إجمالي الساعات', 'value' => round((float) $records->sum('total_hours'), 1)],
            ],
            'columns' => [
                ['key' => 'date', 'label' => 'التاريخ'],
                ['key' => 'employee', 'label' => 'الموظف'],
                ['key' => 'status', 'label' => 'الحالة'],
                ['key' => 'check_in', 'label' => 'دخول'],
                ['key' => 'check_out', 'label' => 'خروج'],
                ['key' => 'hours', 'label' => 'الساعات'],
            ],
            'rows' => $records->map(fn (Attendance $a) => [
                'date' => $a->date?->format('Y-m-d') ?? $a->date,
                'employee' => $a->employee ? trim($a->employee->first_name . ' ' . $a->employee->last_name) : '—',
                'status' => $statusMap[$a->status] ?? $a->status,
                'check_in' => $a->check_in ? Carbon::parse($a->check_in)->format('H:i') : '—',
                'check_out' => $a->check_out ? Carbon::parse($a->check_out)->format('H:i') : '—',
                'hours' => $a->total_hours ?? '—',
            ])->all(),
        ];
    }

    protected function reportSalaries(array $filters): array
    {
        $query = Salary::with(['employee.department'])->orderByDesc('payment_date');

        if ($filters['start_date']) {
            $query->whereBetween('payment_date', [$filters['start_date'], $filters['end_date']]);
        }

        $salaries = $query->limit(2000)->get();

        return [
            'summary' => [
                ['label' => 'عدد المسيرات', 'value' => $salaries->count()],
                ['label' => 'صافي الرواتب', 'value' => number_format((float) $salaries->sum('net_salary'), 0) . ' ج.م'],
                ['label' => 'إجمالي البدلات', 'value' => number_format((float) $salaries->sum('bonus'), 0) . ' ج.م'],
            ],
            'columns' => [
                ['key' => 'employee', 'label' => 'الموظف'],
                ['key' => 'department', 'label' => 'القسم'],
                ['key' => 'payment_date', 'label' => 'تاريخ الدفع'],
                ['key' => 'base', 'label' => 'أساسي', 'type' => 'money'],
                ['key' => 'bonus', 'label' => 'بدلات', 'type' => 'money'],
                ['key' => 'deductions', 'label' => 'خصومات', 'type' => 'money'],
                ['key' => 'net', 'label' => 'الصافي', 'type' => 'money'],
                ['key' => 'status', 'label' => 'الحالة'],
            ],
            'rows' => $salaries->map(fn (Salary $s) => [
                'employee' => $s->employee ? trim($s->employee->first_name . ' ' . $s->employee->last_name) : '—',
                'department' => $s->employee?->department?->name ?? '—',
                'payment_date' => $s->payment_date?->format('Y-m-d') ?? '—',
                'base' => $s->base_salary,
                'bonus' => $s->bonus,
                'deductions' => $s->deductions,
                'net' => $s->net_salary,
                'status' => $s->status,
            ])->all(),
        ];
    }

    protected function reportDepartments(array $filters): array
    {
        $departments = Department::with('manager')
            ->withCount(['employees', 'projects'])
            ->orderBy('name')
            ->get();

        return [
            'summary' => [
                ['label' => 'عدد الأقسام', 'value' => $departments->count()],
                ['label' => 'إجمالي الموظفين', 'value' => $departments->sum('employees_count')],
                ['label' => 'إجمالي المشاريع', 'value' => $departments->sum('projects_count')],
            ],
            'columns' => [
                ['key' => 'name', 'label' => 'القسم'],
                ['key' => 'manager', 'label' => 'المدير'],
                ['key' => 'employees', 'label' => 'الموظفون', 'type' => 'number'],
                ['key' => 'projects', 'label' => 'المشاريع', 'type' => 'number'],
                ['key' => 'active', 'label' => 'نشط'],
            ],
            'rows' => $departments->map(fn (Department $d) => [
                'name' => $d->name,
                'manager' => $d->manager ? trim($d->manager->first_name . ' ' . $d->manager->last_name) : '—',
                'employees' => $d->employees_count,
                'projects' => $d->projects_count,
                'active' => $d->is_active ? 'نعم' : 'لا',
            ])->all(),
        ];
    }

    protected function reportUsers(array $filters): array
    {
        $users = User::with('roles')->orderBy('name')->get();

        return [
            'summary' => [
                ['label' => 'إجمالي المستخدمين', 'value' => $users->count()],
                ['label' => 'لديهم دور نظام', 'value' => $users->filter(fn (User $u) => $u->roles->isNotEmpty())->count()],
            ],
            'columns' => [
                ['key' => 'name', 'label' => 'الاسم'],
                ['key' => 'email', 'label' => 'البريد'],
                ['key' => 'roles', 'label' => 'الأدوار'],
                ['key' => 'created_at', 'label' => 'تاريخ الإنشاء'],
            ],
            'rows' => $users->map(fn (User $u) => [
                'name' => $u->name,
                'email' => $u->email,
                'roles' => $u->roles->pluck('name')->join('، ') ?: '—',
                'created_at' => $u->created_at?->format('Y-m-d'),
            ])->all(),
        ];
    }
}
