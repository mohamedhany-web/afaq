<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-messages')->only([
            'index', 'show', 'unreadCount', 'getRecentMessages', 'markAsRead',
        ]);
        $this->middleware('permission:create-messages|send-messages')->only(['create', 'store']);
        $this->middleware('permission:reply-messages')->only(['reply']);
        $this->middleware('permission:mark-messages-important')->only(['markAsImportant']);
        $this->middleware('permission:delete-messages')->only(['destroy']);
    }

    /**
     * Display a listing of messages
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all');
        $search = $request->get('search');

        $query = Message::where(function($q) use ($user) {
            $q->where('receiver_id', $user->id)
              ->where('is_deleted_by_receiver', false)
              ->orWhere(function($q2) use ($user) {
                  $q2->where('sender_id', $user->id)
                     ->where('is_deleted_by_sender', false);
              });
        });

        // Apply filters
        switch ($filter) {
            case 'unread':
                $query->where('receiver_id', $user->id)->where('is_read', false);
                break;
            case 'important':
                $query->where('is_important', true);
                break;
            case 'sent':
                $query->where('sender_id', $user->id);
                break;
            case 'urgent':
                $query->where('priority', 'urgent');
                break;
        }

        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $messages = $query->with(['sender', 'receiver', 'parentMessage'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get unread count for sidebar
        $unreadCount = Message::where('receiver_id', $user->id)
            ->where('is_read', false)
            ->where('is_deleted_by_receiver', false)
            ->count();

        // Log for debugging
        \Log::info('Messages loaded for user', [
            'user_id' => $user->id,
            'total_messages' => $messages->total(),
            'unread_count' => $unreadCount
        ]);

        return view('messages.index', compact('messages', 'unreadCount', 'filter', 'search'));
    }

    /**
     * Show the form for creating a new message
     */
    public function create(Request $request)
    {
        $replyTo = null;
        if ($request->has('reply_to')) {
            $replyTo = Message::findOrFail($request->reply_to);
        }

        // Get users based on permissions
        $users = $this->getAvailableUsers();

        return view('messages.create', compact('users', 'replyTo'));
    }

    /**
     * Store a newly created message
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'type' => 'required|in:direct,group,announcement',
            'priority' => 'required|in:low,normal,high,urgent',
            'is_important' => 'boolean',
            'parent_message_id' => 'nullable|exists:messages,id',
        ]);

        // Authorization: ensure receiver is allowed for the current user
        $currentUser = auth()->user();
        if (!$this->isReceiverAllowed((int) $request->receiver_id, $currentUser)) {
            abort(403, 'غير مصرح بإرسال رسالة لهذا المستخدم');
        }

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject,
            'body' => $request->body,
            'type' => $request->type,
            'priority' => $request->priority,
            'is_important' => $request->boolean('is_important'),
            'parent_message_id' => $request->parent_message_id,
        ]);

        // Create notification for recipient
        $this->createNotification($message);

        // Log the message creation for debugging
        \Log::info('Message created', [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'receiver_id' => $message->receiver_id,
            'subject' => $message->subject
        ]);

        return redirect()->route('messages.index')
            ->with('success', 'تم إرسال الرسالة بنجاح');
    }

    /**
     * Display the specified message
     */
    public function show(Message $message)
    {
        $user = auth()->user();
        
        if ($message->receiver_id !== $user->id && $message->sender_id !== $user->id) {
            abort(403, 'غير مصرح لك بالوصول لهذه الرسالة');
        }

        // Mark as read if user is recipient
        if ($message->receiver_id === $user->id && !$message->is_read) {
            $message->markAsRead();
        }

        // Get replies if any
        $replies = $message->replies()
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('messages.show', compact('message', 'replies'));
    }

    /**
     * Reply to a message
     */
    public function reply(Request $request, Message $message)
    {
        $request->validate([
            'body' => 'required|string|max:10000',
        ]);

        $reply = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $message->sender_id,
            'subject' => 'Re: ' . $message->subject,
            'body' => $request->body,
            'type' => 'direct',
            'priority' => $message->priority,
            'parent_message_id' => $message->id,
        ]);

        $this->createNotification($reply);

        return redirect()->route('messages.show', $message)
            ->with('success', 'تم إرسال الرد بنجاح');
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Message $message): JsonResponse
    {
        if ($message->receiver_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->markAsRead();

        return response()->json(['success' => true], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Mark message as important
     */
    public function markAsImportant(Message $message): JsonResponse
    {
        if ($message->receiver_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->markAsImportant();

        return response()->json(['success' => true], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get unread messages count
     */
    public function unreadCount(): JsonResponse
    {
        $userId = auth()->id();
        $count = Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->where('is_deleted_by_receiver', false)
            ->count();
            
        // Log for debugging
        \Log::info('Unread count requested', [
            'user_id' => $userId,
            'count' => $count
        ]);
            
        return response()->json(['count' => $count], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get recent messages for sidebar
     */
    public function getRecentMessages(): JsonResponse
    {
        $messages = Message::where('receiver_id', auth()->id())
            ->where('is_deleted_by_receiver', false)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($message) {
                return [
                    'id' => $message->id,
                    'subject' => $message->subject,
                    'sender_name' => $message->sender->name,
                    'is_read' => $message->is_read,
                    'priority' => $message->priority,
                    'priority_color' => $message->priority_color,
                    'created_at' => $message->created_at->diffForHumans(),
                ];
            });

        return response()->json($messages, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Delete message
     */
    public function destroy(Message $message)
    {
        $user = auth()->user();
        
        if ($message->receiver_id !== $user->id && $message->sender_id !== $user->id) {
            abort(403, 'غير مصرح لك بحذف هذه الرسالة');
        }

        // Mark as deleted by the appropriate user
        if ($message->receiver_id === $user->id) {
            $message->update(['is_deleted_by_receiver' => true]);
        } else {
            $message->update(['is_deleted_by_sender' => true]);
        }

        // If both users deleted it, actually delete the record
        if ($message->is_deleted_by_sender && $message->is_deleted_by_receiver) {
            $message->delete();
        }

        return redirect()->route('messages.index')
            ->with('success', 'تم حذف الرسالة بنجاح');
    }

    /**
     * Get available users based on permissions
     */
    private function getAvailableUsers()
    {
        $user = auth()->user();
        
        // Super admin can message everyone
        if ($user->hasRole('super_admin')) {
            return User::where('id', '!=', $user->id)->get();
        }
        
        // Admin can message everyone except super admins
        if ($user->hasRole('admin')) {
            return User::where('id', '!=', $user->id)
                ->whereDoesntHave('roles', function($q) {
                    $q->where('name', 'super_admin');
                })
                ->get();
        }

        // For all other users (employees, developers, designers, PMs, etc.):
        // Only allow recipients who share a project with the user, plus project managers of those projects
        $projectIds = Project::whereHas('teamMembers', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orWhere('project_manager_id', $user->id)
            ->pluck('id');

        if ($projectIds->isEmpty()) {
            return collect();
        }

        // Collect team members on these projects
        $teamUserIds = DB::table('project_team_members')
            ->whereIn('project_id', $projectIds)
            ->pluck('user_id');

        // Collect project managers of these projects
        $managerUserIds = Project::whereIn('id', $projectIds)
            ->whereNotNull('project_manager_id')
            ->pluck('project_manager_id');

        $allowedIds = $teamUserIds->merge($managerUserIds)
            ->unique()
            ->filter(fn($id) => (int)$id !== (int)$user->id)
            ->values();

        return User::whereIn('id', $allowedIds)->get();
    }

    /**
     * Check if a receiver is allowed for the current user per project membership rules
     */
    private function isReceiverAllowed(int $receiverId, $currentUser): bool
    {
        // Super admin and admin bypass
        if ($currentUser->hasRole('super_admin') || $currentUser->hasRole('admin')) {
            return $receiverId !== (int) $currentUser->id;
        }

        // Build allowed set: users who share a project with current user, and PMs of those projects
        $projectIds = Project::whereHas('teamMembers', function($q) use ($currentUser) {
                $q->where('user_id', $currentUser->id);
            })
            ->orWhere('project_manager_id', $currentUser->id)
            ->pluck('id');

        if ($projectIds->isEmpty()) {
            return false;
        }

        $teamUserIds = DB::table('project_team_members')
            ->whereIn('project_id', $projectIds)
            ->pluck('user_id');

        $managerUserIds = Project::whereIn('id', $projectIds)
            ->whereNotNull('project_manager_id')
            ->pluck('project_manager_id');

        $allowedIds = $teamUserIds->merge($managerUserIds)
            ->unique()
            ->filter(fn($id) => (int)$id !== (int)$currentUser->id)
            ->values()
            ->all();

        return in_array($receiverId, $allowedIds, true);
    }

    /**
     * Create notification for message
     */
    private function createNotification(Message $message)
    {
        Notification::create([
            'user_id' => $message->receiver_id,
            'type' => 'message',
            'title' => 'رسالة جديدة من ' . $message->sender->name,
            'message' => $message->subject,
            'data' => [
                'message_id' => $message->id,
                'sender_name' => $message->sender->name,
                'priority' => $message->priority,
            ],
        ]);
    }
}
