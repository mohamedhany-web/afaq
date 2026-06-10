<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CrmScopeService;
use Illuminate\Support\Facades\Auth;

class CrmTeamMemberController extends Controller
{
    public function show(User $member)
    {
        $scope = CrmScopeService::for(Auth::user());

        if (!$scope->canViewTeamMember($member)) {
            abort(403);
        }

        $member->load('employee');

        $today = now()->startOfDay();

        $deals = $scope->salesQuery()
            ->where('assigned_to', $member->id)
            ->with(['client', 'project'])
            ->latest('updated_at')
            ->get();

        $clients = $scope->clientsQuery()
            ->where(function ($q) use ($member) {
                $q->where('created_by', $member->id);

                if ($member->employee?->id) {
                    $q->orWhere('assigned_to', $member->employee->id);
                }
            })
            ->withCount('sales')
            ->latest()
            ->limit(20)
            ->get();

        $todayDeals = $deals->filter(fn ($d) => $d->updated_at >= $today);
        $todayClients = $clients->filter(fn ($c) => $c->created_at >= $today);

        $stats = [
            'total_deals' => $deals->count(),
            'active_deals' => $deals->whereIn('stage', ['lead', 'prospect', 'proposal', 'negotiation'])->count(),
            'won_deals' => $deals->where('stage', 'closed_won')->count(),
            'pipeline_value' => $deals->whereNotIn('stage', ['closed_lost'])->sum('estimated_value'),
            'today_updates' => $todayDeals->count(),
            'today_clients' => $todayClients->count(),
        ];

        $stageLabels = [
            'lead' => 'عميل محتمل',
            'prospect' => 'مهتم',
            'proposal' => 'عرض سعر',
            'negotiation' => 'تفاوض',
            'closed_won' => 'تم البيع',
            'closed_lost' => 'خسارة',
        ];

        return view('crm.team-members.show', compact(
            'member', 'deals', 'clients', 'stats', 'stageLabels', 'todayDeals', 'todayClients'
        ));
    }
}
