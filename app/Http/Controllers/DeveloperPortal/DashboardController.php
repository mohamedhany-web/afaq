<?php

namespace App\Http\Controllers\DeveloperPortal;

use App\Http\Controllers\Controller;
use App\Services\DeveloperPortalService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(protected DeveloperPortalService $portal) {}

    public function index()
    {
        $account = Auth::guard('developer')->user();
        $developer = $this->portal->developer($account);
        $projectsQuery = $this->portal->projectsQuery($account);

        $stats = [
            'projects' => (clone $projectsQuery)->count(),
            'active_listings' => (clone $projectsQuery)->where('listing_status', 'active')->count(),
            'total_units' => (clone $projectsQuery)->sum('total_units'),
            'available_units' => (clone $projectsQuery)->sum('available_units'),
            'portfolio' => $developer->portfolioItems()->where('is_published', true)->count(),
        ];

        $recentProjects = (clone $projectsQuery)->latest()->limit(5)->get();

        return view('developer-portal.dashboard', compact('developer', 'account', 'stats', 'recentProjects'));
    }
}
