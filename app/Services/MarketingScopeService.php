<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Employee;
use App\Models\MarketingActivity;
use App\Models\MarketingCampaign;
use App\Models\MarketingPlan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class MarketingScopeService
{
    public function __construct(protected User $user) {}

    public static function for(User $user): self
    {
        return new self($user);
    }

    public function isAdminScope(): bool
    {
        return $this->user->hasRole(['super_admin', 'admin']);
    }

    public function isManagerScope(): bool
    {
        return $this->isAdminScope() || $this->user->isMarketingManager();
    }

    public function campaignsQuery(): Builder
    {
        $query = MarketingCampaign::query();

        if ($this->isAdminScope()) {
            return $query;
        }

        if ($this->user->isMarketingManager()) {
            $teamIds = $this->teamUserIds();

            return $query->where(function ($q) use ($teamIds) {
                $q->where('manager_id', $this->user->id)
                    ->orWhere('created_by', $this->user->id)
                    ->orWhereIn('manager_id', $teamIds);
            });
        }

        return $query->where(function ($q) {
            $q->where('manager_id', $this->user->id)
                ->orWhere('created_by', $this->user->id)
                ->orWhereHas('activities', fn ($a) => $a->where('assigned_to', $this->user->id));
        });
    }

    public function activitiesQuery(): Builder
    {
        $query = MarketingActivity::query();

        if ($this->isAdminScope() || $this->user->isMarketingManager()) {
            if ($this->isAdminScope()) {
                return $query;
            }

            $teamIds = $this->teamUserIds();
            $campaignIds = $this->campaignsQuery()->pluck('id');

            return $query->where(function ($q) use ($teamIds, $campaignIds) {
                $q->whereIn('assigned_to', $teamIds)
                    ->orWhere('assigned_by', $this->user->id)
                    ->orWhereIn('campaign_id', $campaignIds);
            });
        }

        return $query->where('assigned_to', $this->user->id);
    }

    public function leadsQuery(): Builder
    {
        $query = Client::query();

        if ($this->isAdminScope()) {
            return $query->where(function ($q) {
                $q->whereNotNull('marketing_campaign_id')
                    ->orWhereNotNull('lead_source')
                    ->orWhere('created_by', $this->user->id);
            });
        }

        if ($this->user->isMarketingManager()) {
            $campaignIds = $this->campaignsQuery()->pluck('id');
            $teamIds = $this->teamUserIds();

            return $query->where(function ($q) use ($campaignIds, $teamIds) {
                $q->whereIn('marketing_campaign_id', $campaignIds)
                    ->orWhereIn('created_by', $teamIds);
            });
        }

        return $query->where('created_by', $this->user->id);
    }

    public function employeesQuery(): Builder
    {
        $dept = MarketingEmployeeService::marketingDepartment();

        return Employee::query()
            ->where('department_id', $dept->id)
            ->whereHas('user.roles', fn ($r) => $r->whereIn(
                'name',
                array_merge(
                    MarketingEmployeeService::LEGACY_MANAGER_ROLES,
                    MarketingEmployeeService::LEGACY_REP_ROLES
                )
            ));
    }

    public function teamUserIds(): array
    {
        $ids = $this->employeesQuery()
            ->whereHas('user')
            ->with('user:id')
            ->get()
            ->pluck('user.id')
            ->filter()
            ->values()
            ->all();

        $ids[] = $this->user->id;

        return array_values(array_unique($ids));
    }

    public function assignableUsers(): array
    {
        return User::query()
            ->whereIn('id', $this->teamUserIds())
            ->orderBy('name')
            ->get(['id', 'name'])
            ->all();
    }

    public function plansQuery(): Builder
    {
        $query = MarketingPlan::query();

        if ($this->isAdminScope()) {
            return $query;
        }

        if ($this->isManagerScope()) {
            $teamIds = $this->teamUserIds();

            return $query->where(function ($q) use ($teamIds) {
                $q->whereIn('manager_id', $teamIds)
                    ->orWhere('created_by', $this->user->id)
                    ->orWhereHas('activities', fn ($a) => $a->whereIn('assigned_to', $teamIds));
            });
        }

        return $query->where(function ($q) {
            $q->whereHas('activities', fn ($a) => $a->where('assigned_to', $this->user->id))
                ->orWhere('status', MarketingPlan::STATUS_ACTIVE);
        });
    }
}
