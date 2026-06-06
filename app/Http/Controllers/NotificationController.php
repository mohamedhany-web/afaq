<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationInboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationInboxService $inbox,
    ) {}

    public function index(Request $request)
    {
        if ($request->boolean('dropdown')) {
            $user = auth()->user();
            $stats = $this->inbox->stats($user);
            $notifications = $user->notifications()
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            return response()->view('notifications.partials.dropdown-panel', [
                'notifications' => $notifications,
                'unreadCount' => $stats['unread'],
            ]);
        }

        if ($request->boolean('json') || $request->wantsJson()) {
            $notifications = auth()->user()->notifications()
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();

            $stats = $this->inbox->stats(auth()->user());

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $stats['unread'],
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        $user = auth()->user();
        $filter = $request->get('filter', config('notifications.default_filter', 'unread'));
        $search = $request->get('search');
        $archive = $request->boolean('archive');

        $stats = $this->inbox->stats($user);
        $notifications = $this->inbox->paginate($user, $filter, $search, $archive);
        $grouped = $this->inbox->groupByDate($notifications->getCollection());

        $highVolume = $stats['total'] >= config('notifications.high_volume_threshold', 500);
        $listCapped = $filter === 'all' && ! $archive;

        return view('notifications.index', [
            'notifications' => $notifications,
            'grouped' => $grouped,
            'filter' => $filter,
            'search' => $search,
            'archive' => $archive,
            'stats' => $stats,
            'highVolume' => $highVolume,
            'listCapped' => $listCapped,
            'totalCount' => $stats['total'],
            'unreadCount' => $stats['unread'],
            'todayCount' => $stats['today'],
            'weekCount' => $stats['week'],
            'readCount' => $stats['read'],
            'crmCount' => $stats['crm'],
            'projectCount' => $stats['project'],
            'messageCount' => $stats['message'],
            'taskCount' => $stats['task'],
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        $user = auth()->user();
        $stats = $this->inbox->stats($user);
        $latest = $user->notifications()->unread()->latest()->first();

        return response()->json([
            'count' => $stats['unread'],
            'latest' => $latest ? [
                'id' => $latest->id,
                'title' => $latest->title,
                'message' => $latest->message,
                'type' => $latest->type,
                'url' => $latest->data['url'] ?? route('notifications.index'),
            ] : null,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();
        $this->inbox->forgetStats(auth()->id());

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $user = auth()->user();
        $query = $this->inbox->buildQuery(
            $user,
            $request->get('filter', 'unread'),
            $request->get('search'),
            $request->boolean('archive'),
        );

        $query->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->inbox->forgetStats($user->id);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'تم تحديد الإشعارات كمقروءة.');
    }

    public function clearRead(Request $request): RedirectResponse
    {
        $days = (int) $request->get('days', config('notifications.prune_read_after_days', 30));
        $deleted = $this->inbox->pruneReadForUser(auth()->user(), $days);

        return redirect()
            ->route('notifications.index', ['filter' => 'unread'])
            ->with('success', "تم حذف {$deleted} إشعار مقروء قديم.");
    }

    public function destroy(Request $request, Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403);
        }

        $notification->delete();
        $this->inbox->forgetStats(auth()->id());

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->back()
            ->with('success', 'تم حذف الإشعار.');
    }
}
