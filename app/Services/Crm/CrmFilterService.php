<?php

namespace App\Services\Crm;

use App\Models\Client;
use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use App\Services\CrmEmployeeService;
use App\Services\CrmScopeService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CrmFilterService
{
    public function __construct(protected CrmScopeService $scope) {}

    public static function for(User $user): self
    {
        return new self(CrmScopeService::for($user));
    }

    public function scope(): CrmScopeService
    {
        return $this->scope;
    }

    /** @return Collection<int, User> */
    public function salesReps(): Collection
    {
        if ($this->scope->hasFullAccess() || $this->scope->user()->canAccessOperations()) {
            return User::role(array_merge(
                CrmEmployeeService::LEGACY_EMPLOYEE_ROLES,
                CrmEmployeeService::LEGACY_TEAM_LEADER_ROLES,
            ))
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        if ($this->scope->isManagerScope()) {
            return User::query()
                ->whereIn('id', $this->scope->managedTeamMemberUserIds())
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return User::whereKey($this->scope->user()->id)->get(['id', 'name']);
    }

    public function showSalesRepFilter(): bool
    {
        return $this->scope->hasFullAccess()
            || $this->scope->isManagerScope()
            || $this->scope->user()->canAccessOperations();
    }

    public function showCreatorFilter(): bool
    {
        return $this->creatorUsers()->isNotEmpty();
    }

    /** @return string[] */
    public function clientFilterKeys(): array
    {
        return [
            'search', 'sales_rep', 'created_by', 'mine', 'status', 'lead_stage', 'deal_stage', 'has_deals',
            'unassigned', 'client_type', 'lead_source', 'created_from', 'created_to', 'advanced',
        ];
    }

    /** @return array<int|string, mixed> */
    public function clientListRelations(): array
    {
        return [
            'assignedEmployee',
            'createdBy:id,name',
            'sales',
            'staffNotes' => fn ($q) => $q->latest()->limit(1),
            'latestInteraction' => fn ($q) => $q
                ->where('status', \App\Models\CrmFollowUp::STATUS_COMPLETED)
                ->whereNotNull('notes')
                ->where('notes', '!=', '')
                ->latest('completed_at')
                ->limit(1),
            'followUps' => fn ($q) => $q->where('status', \App\Models\CrmFollowUp::STATUS_SCHEDULED)
                ->orderBy('scheduled_at')
                ->limit(1),
        ];
    }

    /** @return Collection<int, User> */
    public function creatorUsers(): Collection
    {
        if ($this->scope->hasFullAccess() || $this->scope->user()->canAccessOperations()) {
            return User::query()->orderBy('name')->get(['id', 'name']);
        }

        if ($this->scope->isManagerScope()) {
            $ids = $this->scope->managedTeamMemberUserIds();
            $ids[] = $this->scope->user()->id;

            return User::query()->whereIn('id', array_unique($ids))->orderBy('name')->get(['id', 'name']);
        }

        return User::whereKey($this->scope->user()->id)->get(['id', 'name']);
    }

    /** @return string[] */
    public function saleFilterKeys(): array
    {
        return [
            'search', 'sales_rep', 'stage', 'project_id', 'min_value', 'max_value',
            'updated_from', 'updated_to', 'show_closed', 'advanced',
        ];
    }

    /** @return string[] */
    public function followUpFilterKeys(bool $enhanced = false): array
    {
        $keys = ['search', 'sales_rep', 'status', 'type', 'date', 'view', 'bucket', 'advanced'];

        if ($enhanced) {
            return array_merge($keys, ['client_status', 'client_lead_stage', 'date_from', 'date_to', 'client_unassigned', 'overdue_only']);
        }

        return $keys;
    }

    public function applyFollowUpFilters(Builder $query, Request $request): Builder
    {
        $salesRepId = $this->resolveSalesRepId($request);

        return $query
            ->when($request->search, function ($q) use ($request) {
                $s = '%' . $request->search . '%';
                $q->where(function ($sub) use ($s) {
                    $sub->where('notes', 'like', $s)
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', $s)
                            ->orWhere('phone', 'like', $s)
                            ->orWhere('email', 'like', $s)
                            ->orWhere('company_name', 'like', $s));
                });
            })
            ->when($salesRepId, fn ($q) => $q->where('user_id', $salesRepId))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->type, fn ($q) => $q->where('interaction_type', $request->type))
            ->when($request->client_status, fn ($q) => $q->whereHas('client', fn ($c) => $c->where('status', $request->client_status)))
            ->when($request->client_lead_stage, fn ($q) => $q->whereHas('client', fn ($c) => $c->where('lead_stage', $request->client_lead_stage)))
            ->when($request->boolean('client_unassigned'), fn ($q) => $q->whereHas('client', fn ($c) => $c->whereNull('assigned_to')))
            ->when($request->boolean('overdue_only'), fn ($q) => $q
                ->where('status', 'scheduled')
                ->where('scheduled_at', '<', now()));
    }

    public function hasActiveFilters(Request $request, array $keys): bool
    {
        foreach ($keys as $key) {
            if (in_array($key, ['unassigned', 'show_closed', 'advanced', 'client_unassigned', 'overdue_only', 'has_units', 'mine'], true)) {
                if ($request->boolean($key)) {
                    return true;
                }
                continue;
            }

            if ($request->filled($key)) {
                return true;
            }
        }

        return false;
    }

    public function applyClientFilters(Builder $query, Request $request): Builder
    {
        $scope = $this->scope;
        $scopedSaleClientIds = fn () => $scope->salesQuery()->distinct()->pluck('client_id');

        return $query
            ->when($request->search, function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('company_name', 'like', $search)
                        ->orWhere('address', 'like', $search)
                        ->orWhere('notes', 'like', $search)
                        ->orWhere('lead_source', 'like', $search);
                });
            })
            ->when($request->filled('sales_rep'), fn ($q) => $this->applyClientSalesRepFilter($q, (int) $request->sales_rep))
            ->when($request->filled('created_by'), fn ($q) => $q->where('created_by', (int) $request->created_by))
            ->when($request->boolean('mine'), function ($q) use ($scope) {
                $userId = (int) $scope->user()->id;
                $employeeId = $scope->user()->employee?->id;
                $q->where(function ($sub) use ($userId, $employeeId, $scope) {
                    $sub->where('created_by', $userId);
                    if ($employeeId) {
                        $sub->orWhere('assigned_to', $employeeId);
                    }
                    $sub->orWhereIn('id', $scope->salesQuery()->where('assigned_to', $userId)->distinct()->pluck('client_id'));
                });
            })
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->lead_stage, fn ($q) => $q->where('lead_stage', $request->lead_stage))
            ->when($request->client_type, fn ($q) => $q->where('client_type', Client::normalizeType($request->client_type)))
            ->when($request->lead_source, fn ($q) => $q->where('lead_source', Client::normalizeLeadSource($request->lead_source)))
            ->when($request->boolean('unassigned'), fn ($q) => $q->whereNull('assigned_to'))
            ->when($request->created_from, fn ($q) => $q->whereDate('created_at', '>=', $request->created_from))
            ->when($request->created_to, fn ($q) => $q->whereDate('created_at', '<=', $request->created_to))
            ->when($request->deal_stage, function ($q) use ($request, $scope) {
                $ids = $scope->salesQuery()->where('stage', $request->deal_stage)->distinct()->pluck('client_id');
                $q->whereIn('id', $ids);
            })
            ->when($request->has_deals === '1', fn ($q) => $q->whereIn('id', $scopedSaleClientIds()))
            ->when($request->has_deals === '0', fn ($q) => $q->whereNotIn('id', $scopedSaleClientIds()));
    }

    public function applySaleFilters(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->search, function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($sub) use ($search) {
                    $sub->where('product_service', 'like', $search)
                        ->orWhereHas('client', fn ($c) => $c->where('name', 'like', $search)->orWhere('phone', 'like', $search))
                        ->orWhereHas('project', fn ($p) => $p->where('name', 'like', $search));
                });
            })
            ->when($request->filled('sales_rep'), fn ($q) => $q->where('assigned_to', (int) $request->sales_rep))
            ->when($request->stage, fn ($q) => $q->where('stage', $request->stage))
            ->when($request->project_id, fn ($q) => $q->where('project_id', $request->project_id))
            ->when($request->filled('min_value'), fn ($q) => $q->where('estimated_value', '>=', (float) $request->min_value))
            ->when($request->filled('max_value'), fn ($q) => $q->where('estimated_value', '<=', (float) $request->max_value))
            ->when($request->updated_from, fn ($q) => $q->whereDate('updated_at', '>=', $request->updated_from))
            ->when($request->updated_to, fn ($q) => $q->whereDate('updated_at', '<=', $request->updated_to));
    }

    /** @return string[] */
    public function projectFilterKeys(): array
    {
        return [
            'search', 'inventory_source', 'listing_status', 'developer_id', 'property_type',
            'price_min', 'price_max', 'project_type', 'city', 'advanced',
        ];
    }

    public function applyProjectFilters(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->search, fn ($q) => $q->where(function ($sub) use ($request) {
                $sub->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%')
                    ->orWhere('developer_name', 'like', '%' . $request->search . '%')
                    ->orWhereHas('units', fn ($u) => $u
                        ->where('code', 'like', '%' . $request->search . '%')
                        ->orWhere('apartment_number', 'like', '%' . $request->search . '%'));
            }))
            ->when($request->inventory_source, fn ($q) => $q->where('inventory_source', $request->inventory_source))
            ->when($request->listing_status, fn ($q) => $q->where('listing_status', $request->listing_status))
            ->when($request->developer_id, fn ($q) => $q->where('real_estate_developer_id', (int) $request->developer_id))
            ->when($request->filled('price_min'), fn ($q) => $q->where('price_from', '>=', (float) $request->price_min))
            ->when($request->filled('price_max'), fn ($q) => $q->where('price_to', '<=', (float) $request->price_max))
            ->when($request->project_type, fn ($q) => $q->where('project_type', $request->project_type))
            ->when($request->property_type, function ($q) use ($request) {
                $type = $request->property_type;
                $q->where(function ($inner) use ($type) {
                    $inner->where('property_type', $type)
                        ->orWhereJsonContains('property_types', $type);
                });
            })
            ->when($request->ownership_type, fn ($q) => $q->where('ownership_type', $request->ownership_type))
            ->when($request->city, fn ($q) => $q->where('city', 'like', '%' . $request->city . '%'))
            ->when($request->boolean('has_units'), fn ($q) => $q->whereHas('units'))
            ->when($request->unit_use_type, fn ($q) => $q->whereHas('units', fn ($u) => $u->where('use_type', $request->unit_use_type)))
            ->when($request->unit_status, fn ($q) => $q->whereHas('units', fn ($u) => $u->where('status', $request->unit_status)))
            ->when($request->direction, fn ($q) => $q->whereHas('units', fn ($u) => $u->where('direction', $request->direction)))
            ->when($request->filled('floor_number'), fn ($q) => $q->whereHas('units', fn ($u) => $u->where('floor_number', $request->floor_number)))
            ->when($request->filled('area_min'), fn ($q) => $q->whereHas('units', fn ($u) => $u->where('area_m2', '>=', (float) $request->area_min)))
            ->when($request->filled('area_max'), fn ($q) => $q->whereHas('units', fn ($u) => $u->where('area_m2', '<=', (float) $request->area_max)));
    }

    public function resolveSalesRepId(Request $request): ?int
    {
        $id = $request->filled('sales_rep')
            ? (int) $request->sales_rep
            : ($request->filled('user_id') ? (int) $request->user_id : ($request->filled('assignee') ? (int) $request->assignee : null));

        if ($id === null) {
            return null;
        }

        return $this->isAllowedSalesRep($id) ? $id : null;
    }

    /** @return Collection<int, Project> */
    public function projectsForFilter(): Collection
    {
        return Project::query()->orderBy('name')->get(['id', 'name']);
    }

    protected function applyClientSalesRepFilter(Builder $query, int $salesRepUserId): Builder
    {
        if (!$this->isAllowedSalesRep($salesRepUserId)) {
            return $query;
        }

        $employeeId = Employee::query()->where('user_id', $salesRepUserId)->value('id');

        return $query->where(function ($sub) use ($salesRepUserId, $employeeId) {
            if ($employeeId) {
                $sub->where('assigned_to', $employeeId);
            }

            $sub->orWhereHas('sales', fn ($s) => $s->where('assigned_to', $salesRepUserId));
        });
    }

    protected function isAllowedSalesRep(int $userId): bool
    {
        if (!$this->showSalesRepFilter()) {
            return $userId === (int) $this->scope->user()->id;
        }

        return $this->salesReps()->contains('id', $userId);
    }
}
