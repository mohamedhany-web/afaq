<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\SalesTeam;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class CrmScopeService
{
    public const LEAD_STAGE_NEW = 'new';

    public const LEAD_STAGES = ['new', 'lead', 'prospect', 'proposal', 'negotiation', 'closed_won', 'closed_lost'];

    /** @return list<string> */
    public static function activeLeadStages(): array
    {
        return ['new', 'lead', 'prospect', 'proposal', 'negotiation'];
    }

    /** @return list<string> */
    public static function closedLeadStages(): array
    {
        return ['closed_won', 'closed_lost'];
    }

    /** @return array<string, string> */
    public static function leadStageLabels(): array
    {
        return [
            'new' => 'New Lead / جديد',
            'lead' => 'عميل محتمل',
            'prospect' => 'مهتم',
            'proposal' => 'عرض سعر',
            'negotiation' => 'تفاوض',
            'closed_won' => 'تم البيع',
            'closed_lost' => 'خسارة',
        ];
    }

    /** @return array<string, array{bg: string, light: string}> */
    public static function clientLeadStageColors(): array
    {
        return [
            'new' => ['bg' => '#2563eb', 'light' => '#eff6ff'],
            'lead' => ['bg' => '#6366f1', 'light' => '#eef2ff'],
            'prospect' => ['bg' => '#3b82f6', 'light' => '#eff6ff'],
            'proposal' => ['bg' => '#0ea5e9', 'light' => '#f0f9ff'],
            'negotiation' => ['bg' => '#f59e0b', 'light' => '#fffbeb'],
            'closed_won' => ['bg' => '#16a34a', 'light' => '#f0fdf4'],
            'closed_lost' => ['bg' => '#ef4444', 'light' => '#fef2f2'],
        ];
    }

    public function __construct(protected User $user) {}

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function user(): User
    {
        return $this->user;
    }

    public function hasFullAccess(): bool
    {
        return $this->user->hasRole(['super_admin', 'admin']);
    }

    public function isDepartmentHeadScope(): bool
    {
        if ($this->hasFullAccess()) {
            return false;
        }

        return $this->user->hasRole(CrmEmployeeService::LEGACY_DEPARTMENT_HEAD_ROLES);
    }

    public function isTeamLeaderScope(): bool
    {
        if ($this->hasFullAccess() || $this->isDepartmentHeadScope()) {
            return false;
        }

        return $this->user->hasRole(CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES)
            || $this->user->managedSalesTeams()->exists();
    }

    public function isManagerScope(): bool
    {
        if ($this->hasFullAccess()) {
            return false;
        }

        return $this->isDepartmentHeadScope() || $this->isTeamLeaderScope();
    }

    public function isRepScope(): bool
    {
        if ($this->hasFullAccess() || $this->isManagerScope()) {
            return false;
        }

        return $this->user->hasRole(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES);
    }

    /** @return int[] */
    public function adminUserIds(): array
    {
        return Cache::remember('crm_admin_user_ids', 300, function () {
            return User::role(['super_admin', 'admin'])->pluck('id')->all();
        });
    }

    public function salesQuery(): Builder
    {
        $query = Sale::query();

        if ($this->hasFullAccess()) {
            return $query;
        }

        if ($this->isManagerScope()) {
            $memberIds = $this->managedTeamMemberUserIds();

            return $query
                ->whereIn('assigned_to', $memberIds)
                ->whereHas('client', fn ($c) => $this->applyClientVisibility($c, false));
        }

        return $query
            ->where('assigned_to', $this->user->id)
            ->whereHas('client', fn ($c) => $this->applyClientVisibility($c, false));
    }

    public function clientsQuery(): Builder
    {
        if ($this->user->canAccessOperations() || $this->user->hasRole('hr')) {
            return Client::query();
        }

        return $this->applyClientVisibility(Client::query(), true);
    }

    protected function applyClientVisibility(Builder $query, bool $includeSalesLink): Builder
    {
        if ($this->hasFullAccess()) {
            return $query;
        }

        if ($this->isManagerScope()) {
            $memberUserIds = $this->managedTeamMemberUserIds();
            $memberEmployeeIds = Employee::whereIn('user_id', $memberUserIds)->pluck('id');

            $query->where(function ($q) use ($memberUserIds, $memberEmployeeIds, $includeSalesLink) {
                $q->whereIn('created_by', $memberUserIds);

                if ($memberEmployeeIds->isNotEmpty()) {
                    $q->orWhereIn('assigned_to', $memberEmployeeIds);
                }

                if ($includeSalesLink) {
                    $q->orWhereHas('sales', fn ($s) => $s->whereIn('assigned_to', $memberUserIds));
                }
            });

            return $query;
        }

        if ($this->isRepScope()) {
            return $this->applyRepClientVisibility($query);
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * مندوب المبيعات يرى فقط:
     * - العملاء/الـ leads التي أضافها بنفسه
     * - العملاء المُرحَّلين إليه من الإدارة (assigned_to)
     */
    protected function applyRepClientVisibility(Builder $query): Builder
    {
        $employeeId = $this->user->employee?->id;

        return $query->where(function ($q) use ($employeeId) {
            $q->where('created_by', $this->user->id);

            if ($employeeId) {
                $q->orWhere('assigned_to', $employeeId);
            }
        });
    }

    public function employeesQuery(): Builder
    {
        $salesDeptId = CrmEmployeeService::salesDepartment()->id;
        $query = Employee::query()->where('department_id', $salesDeptId);

        if ($this->hasFullAccess()) {
            return $query;
        }

        if ($this->isManagerScope()) {
            return $query->whereIn('user_id', $this->managedTeamMemberUserIds());
        }

        return $query->where('user_id', $this->user->id);
    }

    public function managedTeamsQuery(): Builder
    {
        if ($this->hasFullAccess()) {
            return SalesTeam::query();
        }

        if ($this->isDepartmentHeadScope()) {
            $deptId = CrmEmployeeService::salesDepartment()->id;

            return SalesTeam::query()->where('department_id', $deptId);
        }

        return SalesTeam::where('manager_id', $this->user->id);
    }

    /** @return int[] */
    public function managedTeamMemberUserIds(): array
    {
        if ($this->hasFullAccess()) {
            return User::pluck('id')->all();
        }

        if (!$this->isManagerScope()) {
            return [$this->user->id];
        }

        if ($this->isDepartmentHeadScope()) {
            $salesDeptId = CrmEmployeeService::salesDepartment()->id;

            return Employee::query()
                ->where('department_id', $salesDeptId)
                ->where('status', 'active')
                ->whereNotNull('user_id')
                ->pluck('user_id')
                ->push($this->user->id)
                ->unique()
                ->values()
                ->all();
        }

        $ids = collect([$this->user->id]);

        SalesTeam::where('manager_id', $this->user->id)
            ->with('members:id')
            ->get()
            ->each(function (SalesTeam $team) use ($ids) {
                $ids->push(...$team->members->pluck('id'));
            });

        return $ids->unique()->values()->all();
    }

    /** @deprecated */
    public function teamMemberUserIds(): array
    {
        return $this->managedTeamMemberUserIds();
    }

    /** @return int[] */
    public function managedTeamIds(): array
    {
        if ($this->hasFullAccess()) {
            return SalesTeam::pluck('id')->all();
        }

        return SalesTeam::where('manager_id', $this->user->id)->pluck('id')->all();
    }

    public function canViewTeamMember(User $member): bool
    {
        if ($this->hasFullAccess()) {
            return true;
        }

        if ($this->isRepScope()) {
            return (int) $member->id === (int) $this->user->id;
        }

        if ($this->isManagerScope()) {
            return in_array($member->id, $this->managedTeamMemberUserIds(), true);
        }

        return (int) $member->id === (int) $this->user->id;
    }

    public function canViewEmployee(Employee $employee): bool
    {
        if (!$employee->user_id) {
            return $this->hasFullAccess();
        }

        return $this->employeesQuery()
            ->where('id', $employee->id)
            ->exists();
    }

    public function canViewTeam(SalesTeam $team): bool
    {
        if ($this->hasFullAccess()) {
            return true;
        }

        if ((int) $team->manager_id === (int) $this->user->id) {
            return true;
        }

        return $this->isManagerScope()
            && $this->user->managedSalesTeams()->whereKey($team->id)->exists();
    }

    public function assertClientInScope(int $clientId): void
    {
        if ($this->clientsQuery()->where('id', $clientId)->exists()) {
            return;
        }

        abort(403, 'لا يمكنك الوصول إلى هذا العميل.');
    }

    public function assertSaleInScope(Sale $sale): void
    {
        if ($this->salesQuery()->where('id', $sale->id)->exists()) {
            return;
        }

        abort(403, 'لا يمكنك الوصول إلى هذه الصفقة.');
    }

    /** @return int[] */
    public function assignableRepUserIds(): array
    {
        if ($this->hasFullAccess()) {
            return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)->pluck('id')->all();
        }

        if ($this->isManagerScope()) {
            return collect($this->managedTeamMemberUserIds())
                ->filter(fn ($id) => (int) $id !== (int) $this->user->id)
                ->values()
                ->all();
        }

        return [$this->user->id];
    }

    /** مندوبو يمكن للمدير إضافتهم لفريق جديد (قسم المبيعات) */
    public function assignableTeamMemberUserIds(): array
    {
        if ($this->hasFullAccess()) {
            return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)->pluck('id')->all();
        }

        if ($this->isDepartmentHeadScope()) {
            $salesDeptId = CrmEmployeeService::salesDepartment()->id;

            return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
                ->where('id', '!=', $this->user->id)
                ->whereHas('employee', fn ($q) => $q->where('department_id', $salesDeptId))
                ->pluck('id')
                ->all();
        }

        if ($this->isTeamLeaderScope()) {
            $salesDeptId = CrmEmployeeService::salesDepartment()->id;

            return User::role(CrmEmployeeService::LEGACY_EMPLOYEE_ROLES)
                ->where('id', '!=', $this->user->id)
                ->whereDoesntHave('salesTeams')
                ->whereHas('employee', fn ($q) => $q->where('department_id', $salesDeptId))
                ->pluck('id')
                ->all();
        }

        return [];
    }

    /** @return int[] */
    public function assignableEmployeeIds(): array
    {
        return $this->employeesQuery()->pluck('id')->all();
    }

    public static function creatorDisplayName(Client $client): string
    {
        $creator = $client->relationLoaded('createdBy')
            ? $client->getRelation('createdBy')
            : ($client->created_by ? $client->createdBy : null);

        if ($creator) {
            return $creator->name;
        }

        if ($client->assignedEmployee) {
            return trim($client->assignedEmployee->first_name . ' ' . $client->assignedEmployee->last_name) . ' (تعيين)';
        }

        return 'غير محدد';
    }

    public static function creatorIsAdmin(Client $client): bool
    {
        if (!$client->created_by) {
            return false;
        }

        $creator = $client->relationLoaded('createdBy')
            ? $client->getRelation('createdBy')
            : User::find($client->created_by);

        return $creator?->hasRole(['super_admin', 'admin']) ?? false;
    }
}
