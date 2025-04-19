<?php

namespace App\Http\Controllers;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketStatusHistory;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tickets = null;

        // Different views based on user role
        if ($user->isAdmin()) {
            // HC sees all tickets
            $tickets = Ticket::with(['user', 'category'])
                    ->orderBy('created_at', 'desc')
                    ->get();
        } elseif ($user->isIT()) {
            // IT sees tickets assigned to IT department or specifically to them
            $tickets = Ticket::with(['user', 'category'])
                    ->where(function($query) use ($user) {
                        $query->where('assigned_to_department', 'IT')
                              ->orWhere('assigned_to_user_id', $user->id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
        } elseif ($user->isGA()) {
            // GA sees tickets assigned to GA department or specifically to them
            $tickets = Ticket::with(['user', 'category'])
                    ->where(function($query) use ($user) {
                        $query->where('assigned_to_department', 'GA')
                              ->orWhere('assigned_to_user_id', $user->id);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
        } else {
            // Regular users see only their own tickets
            $tickets = Ticket::with(['category'])
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
        }

        return response()->json(['tickets' => $tickets]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Only regular users can create tickets
        if (!$user->isUser()) {
            return response()->json(['message' => 'Only regular users can create tickets'], 403);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:ticket_categories,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
        ]);

        // Generate ticket number
        $validated['ticket_number'] = Ticket::generateTicketNumber();
        $validated['user_id'] = $user->id;
        $validated['status'] = 'new';

        $ticket = Ticket::create($validated);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/' . $ticket->id, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ]);
            }
        }

        // Record status history
        TicketStatusHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'from_status' => '',
            'to_status' => 'new',
            'notes' => 'Ticket created'
        ]);

        return response()->json([
            'ticket' => $ticket->load(['attachments', 'category']),
            'message' => 'Ticket created successfully'
        ]);
    }

    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        // Check permissions
        if (!$this->canViewTicket($user, $ticket)) {
            return response()->json(['message' => 'You are not authorized to view this ticket'], 403);
        }

        $ticket->load(['user', 'category', 'assignedTo', 'messages.user', 'attachments', 'statusHistory.user']);

        // If this user is a regular user, filter out internal messages
        if ($user->isUser()) {
            $ticket->messages = $ticket->messages->filter(function($message) {
                return !$message->is_internal;
            });
        }

        return response()->json(['ticket' => $ticket]);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Check permissions
        if (!$this->canUpdateTicket($user, $ticket)) {
            return response()->json(['message' => 'You are not authorized to update this ticket'], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|required|in:assigned,in_progress,resolved,closed,rejected',
            'assigned_to_user_id' => 'sometimes|nullable|exists:users,id',
            'assigned_to_department' => 'sometimes|nullable|in:IT,GA',
            'notes' => 'sometimes|nullable|string',
        ]);

        $oldStatus = $ticket->status;

        // Update status timestamps
        if (isset($validated['status'])) {
            switch ($validated['status']) {
                case 'assigned':
                    $validated['assigned_at'] = now();
                    break;
                case 'resolved':
                    $validated['resolved_at'] = now();
                    break;
                case 'closed':
                    $validated['closed_at'] = now();
                    break;
            }
        }

        $ticket->update($validated);

        // Log status change
        if (isset($validated['status']) && $oldStatus !== $validated['status']) {
            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'from_status' => $oldStatus,
                'to_status' => $validated['status'],
                'notes' => $request->input('notes')
            ]);
        }

        return response()->json([
            'ticket' => $ticket->fresh(['user', 'category', 'assignedTo']),
            'message' => 'Ticket updated successfully'
        ]);
    }

    // Helper methods for authorization
    private function canViewTicket($user, $ticket)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->id === $ticket->user_id) {
            return true;
        }

        if ($user->isIT() && $ticket->assigned_to_department === 'IT') {
            return true;
        }

        if ($user->isGA() && $ticket->assigned_to_department === 'GA') {
            return true;
        }

        if ($ticket->assigned_to_user_id === $user->id) {
            return true;
        }

        return false;
    }

    private function canUpdateTicket($user, $ticket)
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isIT() && $ticket->assigned_to_department === 'IT') {
            return true;
        }

        if ($user->isGA() && $ticket->assigned_to_department === 'GA') {
            return true;
        }

        if ($ticket->assigned_to_user_id === $user->id) {
            return true;
        }

        return false;
    }
}
