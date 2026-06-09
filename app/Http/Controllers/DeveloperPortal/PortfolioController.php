<?php

namespace App\Http\Controllers\DeveloperPortal;

use App\Http\Controllers\Controller;
use App\Models\DeveloperPortfolioItem;
use App\Services\DeveloperPortalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function __construct(protected DeveloperPortalService $portal) {}

    public function index()
    {
        $account = Auth::guard('developer')->user();
        $items = $this->portal->developer($account)
            ->portfolioItems()
            ->orderBy('sort_order')
            ->paginate(15);

        return view('developer-portal.portfolio.index', compact('items'));
    }

    public function create()
    {
        abort_unless(Auth::guard('developer')->user()->canManagePortfolio(), 403);

        return view('developer-portal.portfolio.create');
    }

    public function store(Request $request)
    {
        $account = Auth::guard('developer')->user();
        abort_unless($account->canManagePortfolio(), 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'project_type' => 'nullable|string|max:40',
            'year' => 'nullable|integer|min:1980|max:' . (date('Y') + 2),
            'sort_order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        DeveloperPortfolioItem::create(array_merge($data, [
            'real_estate_developer_id' => $account->real_estate_developer_id,
            'is_published' => (bool) ($data['is_published'] ?? true),
            'sort_order' => $data['sort_order'] ?? 0,
        ]));

        return redirect()->route('developer.portfolio.index')->with('success', 'تم إضافة سابقة الأعمال');
    }

    public function edit(DeveloperPortfolioItem $item)
    {
        $account = Auth::guard('developer')->user();
        abort_unless((int) $item->real_estate_developer_id === (int) $account->real_estate_developer_id, 404);
        abort_unless($account->canManagePortfolio(), 403);

        return view('developer-portal.portfolio.edit', ['portfolio' => $item]);
    }

    public function update(Request $request, DeveloperPortfolioItem $item)
    {
        $account = Auth::guard('developer')->user();
        abort_unless((int) $item->real_estate_developer_id === (int) $account->real_estate_developer_id, 404);
        abort_unless($account->canManagePortfolio(), 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'project_type' => 'nullable|string|max:40',
            'year' => 'nullable|integer|min:1980|max:' . (date('Y') + 2),
            'sort_order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $item->update(array_merge($data, [
            'is_published' => (bool) ($data['is_published'] ?? false),
        ]));

        return redirect()->route('developer.portfolio.index')->with('success', 'تم التحديث');
    }

    public function destroy(DeveloperPortfolioItem $item)
    {
        $account = Auth::guard('developer')->user();
        abort_unless((int) $item->real_estate_developer_id === (int) $account->real_estate_developer_id, 404);
        abort_unless($account->canManagePortfolio(), 403);

        $item->delete();

        return redirect()->route('developer.portfolio.index')->with('success', 'تم الحذف');
    }
}
