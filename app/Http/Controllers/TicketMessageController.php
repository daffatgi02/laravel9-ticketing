<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Ticket $ticket)
    {
        $user = Auth::user();

        // Check if user can view this ticket
        if (!$this->canViewTicket($user, $ticket)) {
            return response()->json(['message' => 'You are not authorized to view messages for this ticket'], 403);
        }

        $query = TicketMessage::with(['user', 'attachments'])
                ->where('ticket_id', $ticket->id)
                ->orderBy('created_at', 'desc');

        // If regular user, filter out internal messages
        if ($user->role === 'user') {
            $query->where('is_internal', false);
        }

        $messages = $query->get();

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        // Check if user can add messages to this ticket
        if (!$this->canViewTicket($user, $ticket)) {
            return response()->json(['message' => 'You are not authorized to add messages to this ticket'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'is_internal' => 'sometimes|boolean',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
        ]);

        // Regular users cannot send internal messages
        if ($user->role === 'user') {
            $validated['is_internal'] = false;
        }

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_internal' => $validated['is_internal'] ?? false,
        ]);

        // Handle attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/' . $ticket->id . '/' . $message->id, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'message_id' => $message->id,
                    'filename' => $file->getClientOriginalName(),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize()
                ]);
            }
        }

        return response()->json([
            'message' => $message->load(['user', 'attachments']),
            'status' => 'Message added successfully'
        ]);
    }

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
}
