<?php

namespace App\Http\Controllers\DeveloperPortal;

use App\Http\Controllers\Controller;
use App\Services\DeveloperPortalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(protected DeveloperPortalService $portal) {}

    public function edit()
    {
        $account = Auth::guard('developer')->user();
        $developer = $this->portal->developer($account)->load('activeContract');

        return view('developer-portal.profile.edit', compact('account', 'developer'));
    }

    public function update(Request $request)
    {
        $account = Auth::guard('developer')->user();
        abort_unless($account->canManageProjects(), 403);

        $developer = $this->portal->developer($account);

        $data = $request->validate([
            'phone' => 'nullable|string|max:40',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
        ]);

        $developer->update($data);

        return back()->with('success', 'تم تحديث بيانات الشركة');
    }
}
