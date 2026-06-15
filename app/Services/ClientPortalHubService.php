<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientMeetingRequest;
use App\Models\ClientNotification;
use App\Models\ClientWebsiteIssue;
use App\Models\Ticket;
use Illuminate\Support\Collection;

class ClientPortalHubService
{
    public function summaryForClient(Client $client): array
    {
        $clientId = $client->id;

        return [
            'accounts_count' => $client->accounts()->count(),
            'active_accounts' => $client->accounts()->where('is_active', true)->count(),
            'unread_notifications' => ClientNotification::query()
                ->where('client_id', $clientId)
                ->whereNull('read_at')
                ->count(),
            'open_tickets' => Ticket::query()
                ->where('client_id', $clientId)
                ->whereNotIn('status', ['closed', 'resolved'])
                ->count(),
            'open_issues' => ClientWebsiteIssue::query()
                ->where('client_id', $clientId)
                ->whereIn('status', ['open', 'in_progress'])
                ->count(),
            'pending_meetings' => ClientMeetingRequest::query()
                ->where('client_id', $clientId)
                ->where('status', 'pending')
                ->count(),
            'recent_activity' => $this->recentActivity($client),
        ];
    }

    public function adminPulse(): array
    {
        return [
            'pending_meetings' => ClientMeetingRequest::where('status', 'pending')->count(),
            'open_issues' => ClientWebsiteIssue::whereIn('status', ['open', 'in_progress'])->count(),
            'open_tickets' => Ticket::query()
                ->whereNotNull('client_id')
                ->whereNotIn('status', ['closed', 'resolved'])
                ->count(),
            'clients_with_portal' => Client::whereHas('accounts')->count(),
        ];
    }

    protected function recentActivity(Client $client): Collection
    {
        $items = collect();

        ClientWebsiteIssue::query()
            ->where('client_id', $client->id)
            ->latest()
            ->limit(3)
            ->get()
            ->each(fn ($row) => $items->push([
                'type' => 'issue',
                'title' => $row->title ?? $row->reference_code,
                'status' => $row->status,
                'at' => $row->created_at,
                'url' => route('client-website-issues.show', $row),
            ]));

        ClientMeetingRequest::query()
            ->where('client_id', $client->id)
            ->latest()
            ->limit(3)
            ->get()
            ->each(fn ($row) => $items->push([
                'type' => 'meeting',
                'title' => $row->title ?? $row->reference_code,
                'status' => $row->status,
                'at' => $row->created_at,
                'url' => route('client-meeting-requests.show', $row),
            ]));

        Ticket::query()
            ->where('client_id', $client->id)
            ->latest()
            ->limit(3)
            ->get()
            ->each(fn ($row) => $items->push([
                'type' => 'ticket',
                'title' => $row->subject,
                'status' => $row->status,
                'at' => $row->created_at,
                'url' => route('tickets.show', $row),
            ]));

        return $items->sortByDesc('at')->take(8)->values();
    }
}
