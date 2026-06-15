<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientMeetingRequest;
use App\Models\User;
use App\Services\ClientPortalNotifier;
use Illuminate\Http\Request;

class ClientMeetingRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientMeetingRequest::with(['client', 'assignee', 'confirmer'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->integer('client_id'));
        }
        if ($request->filled('search')) {
            $s = '%'.$request->string('search').'%';
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', $s)
                    ->orWhere('description', 'like', $s)
                    ->orWhere('reference_code', 'like', $s);
            });
        }

        $meetingRequests = $query->paginate(20)->withQueryString();
        $clients = Client::query()->orderBy('name')->get(['id', 'name', 'company_name']);

        return view('admin.client-meeting-requests.index', compact('meetingRequests', 'clients'));
    }

    public function show(ClientMeetingRequest $meetingRequest)
    {
        $meetingRequest->load(['client', 'assignee', 'confirmer']);
        $users = User::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.client-meeting-requests.show', compact('meetingRequest', 'users'));
    }

    public function update(Request $request, ClientMeetingRequest $meetingRequest)
    {
        if ($request->input('assigned_to') === '') {
            $request->merge(['assigned_to' => null]);
        }
        foreach (['scheduled_at', 'meeting_link', 'location_notes'] as $key) {
            if ($request->input($key) === '') {
                $request->merge([$key => null]);
            }
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,declined,completed,cancelled',
            'admin_notes' => 'nullable|string|max:10000',
            'response_message' => 'nullable|string|max:10000',
            'scheduled_at' => 'nullable|date',
            'meeting_link' => 'nullable|string|max:2000',
            'location_notes' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $updates = [
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'response_message' => $validated['response_message'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'meeting_link' => $validated['meeting_link'] ?? null,
            'location_notes' => $validated['location_notes'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
        ];

        if ($validated['status'] === 'confirmed' && $meetingRequest->status !== 'confirmed') {
            $updates['confirmed_by'] = $request->user()?->id;
        }

        $prevStatus = $meetingRequest->status;

        $meetingRequest->update($updates);
        $meetingRequest->refresh();

        $cid = (int) $meetingRequest->client_id;
        if ($cid && $prevStatus !== $meetingRequest->status) {
            if ($meetingRequest->status === 'confirmed') {
                ClientPortalNotifier::notify(
                    $cid,
                    'meeting_confirmed',
                    'تم تأكيد طلب الاجتماع',
                    'تم تأكيد الموعد للطلب '.$meetingRequest->reference_code,
                    url('/client/meeting-requests/'.$meetingRequest->id),
                    ['meeting_request_id' => $meetingRequest->id]
                );
            } elseif ($meetingRequest->status === 'declined') {
                ClientPortalNotifier::notify(
                    $cid,
                    'meeting_declined',
                    'تحديث على طلب الاجتماع',
                    'تم تحديث حالة طلب الاجتماع '.$meetingRequest->reference_code,
                    url('/client/meeting-requests/'.$meetingRequest->id),
                    ['meeting_request_id' => $meetingRequest->id]
                );
            } elseif (in_array($meetingRequest->status, ['completed', 'cancelled'], true)) {
                ClientPortalNotifier::notify(
                    $cid,
                    'meeting_status',
                    'تحديث على طلب الاجتماع',
                    'تم تحديث حالة طلب الاجتماع '.$meetingRequest->reference_code.' إلى '.$meetingRequest->status,
                    url('/client/meeting-requests/'.$meetingRequest->id),
                    ['meeting_request_id' => $meetingRequest->id]
                );
            }
        }

        return redirect()
            ->route('client-meeting-requests.show', $meetingRequest)
            ->with('success', 'تم تحديث طلب الاجتماع');
    }
}
