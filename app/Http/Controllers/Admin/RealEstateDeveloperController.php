<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeveloperAccount;
use App\Models\Project;
use App\Models\RealEstateDeveloper;
use App\Services\DeveloperManagementService;
use Illuminate\Http\Request;

class RealEstateDeveloperController extends Controller
{
    public function __construct(protected DeveloperManagementService $developers) {}

    public function index(Request $request)
    {
        $baseQuery = RealEstateDeveloper::query()
            ->withCount(['projects', 'accounts', 'portfolioItems'])
            ->with('activeContract');

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'contracted' => (clone $baseQuery)->whereHas('contracts', fn ($q) => $q->where('status', 'active'))->count(),
            'portal_ready' => (clone $baseQuery)->where('portal_enabled', true)->whereHas('accounts', fn ($q) => $q->where('is_active', true))->count(),
            'projects' => Project::query()->whereNotNull('real_estate_developer_id')->count(),
        ];

        $developers = (clone $baseQuery)
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%');
            }))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->contract === 'active', fn ($q) => $q->whereHas('contracts', fn ($c) => $c->where('status', 'active')))
            ->when($request->contract === 'none', fn ($q) => $q->whereDoesntHave('contracts', fn ($c) => $c->where('status', 'active')))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.developers.index', compact('developers', 'stats'));
    }

    public function create()
    {
        return view('admin.developers.create');
    }

    public function store(Request $request)
    {
        $data = $this->developers->validateDeveloper($request);
        $developer = $this->developers->create($data, $request->user());

        return redirect()
            ->route('admin.developers.show', $developer)
            ->with('success', 'تم إضافة المطور العقاري والتعاقد بنجاح');
    }

    public function show(RealEstateDeveloper $developer)
    {
        $developer->load(['activeContract', 'accounts', 'portfolioItems', 'projects' => fn ($q) => $q->latest()->limit(10)]);

        return view('admin.developers.show', compact('developer'));
    }

    public function edit(RealEstateDeveloper $developer)
    {
        $developer->load(['activeContract', 'accounts']);

        return view('admin.developers.edit', compact('developer'));
    }

    public function update(Request $request, RealEstateDeveloper $developer)
    {
        $rules = $this->developers->validateDeveloper($request, $developer);
        if ($request->filled('portal_account_email')) {
            // allow same email on update when not changing account
        }
        $this->developers->update($developer, $rules, $request->user());

        return redirect()
            ->route('admin.developers.show', $developer)
            ->with('success', 'تم تحديث بيانات المطور والتعاقد');
    }

    public function destroy(RealEstateDeveloper $developer)
    {
        if ($developer->projects()->where('sold_units', '>', 0)->exists()) {
            return back()->with('error', 'لا يمكن حذف مطور مرتبط بوحدات مباعة.');
        }

        $developer->delete();

        return redirect()->route('admin.developers.index')->with('success', 'تم حذف المطور');
    }

    public function togglePortal(RealEstateDeveloper $developer)
    {
        $developer->update(['portal_enabled' => !$developer->portal_enabled]);

        return back()->with('success', $developer->portal_enabled ? 'تم تفعيل بوابة المطور' : 'تم إيقاف بوابة المطور');
    }

    public function resetAccountPassword(Request $request, RealEstateDeveloper $developer, DeveloperAccount $account)
    {
        abort_unless((int) $account->real_estate_developer_id === (int) $developer->id, 404);

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $account->update(['password' => $request->input('password')]);

        return back()->with('success', 'تم تحديث كلمة مرور حساب البوابة');
    }
}
