<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HCController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:hc');
    }

    // For HC to assign tickets to departments
    public function assignTicket(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'assigned_to_department' => 'required|in:IT,GA',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $ticket->status;
        $oldDepartment = $ticket->assigned_to_department;

        $ticket->update([
            'status' => 'assigned',
            'assigned_to_department' => $validated['assigned_to_department'],
            'assigned_at' => now(),
        ]);

        // Record status history
        TicketStatusHistory::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'from_status' => $oldStatus,
            'to_status' => 'assigned',
            'notes' => 'Assigned to ' . $validated['assigned_to_department'] .
                      ' department' . ($oldDepartment ? ' (was: ' . $oldDepartment . ')' : '') .
                      ($validated['notes'] ? '. Notes: ' . $validated['notes'] : '')
        ]);

        return response()->json([
            'ticket' => $ticket->fresh(['user', 'category']),
            'message' => 'Ticket assigned to ' . $validated['assigned_to_department'] . ' department'
        ]);
    }

    // For HC to get list of new tickets that need assignment
    public function getNewTickets()
    {
        $newTickets = Ticket::with(['user', 'category'])
                    ->where('status', 'new')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json(['tickets' => $newTickets]);
    }

    // For HC to view stats about departments
    public function getDepartmentStats()
    {
        $itStats = [
            'total' => Ticket::where('assigned_to_department', 'IT')->count(),
            'new' => Ticket::where('assigned_to_department', 'IT')->where('status', 'new')->count(),
            'assigned' => Ticket::where('assigned_to_department', 'IT')->where('status', 'assigned')->count(),
            'in_progress' => Ticket::where('assigned_to_department', 'IT')->where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('assigned_to_department', 'IT')->where('status', 'resolved')->count(),
            'closed' => Ticket::where('assigned_to_department', 'IT')->where('status', 'closed')->count(),
        ];

        $gaStats = [
            'total' => Ticket::where('assigned_to_department', 'GA')->count(),
            'new' => Ticket::where('assigned_to_department', 'GA')->where('status', 'new')->count(),
            'assigned' => Ticket::where('assigned_to_department', 'GA')->where('status', 'assigned')->count(),
            'in_progress' => Ticket::where('assigned_to_department', 'GA')->where('status', 'in_progress')->count(),
            'resolved' => Ticket::where('assigned_to_department', 'GA')->where('status', 'resolved')->count(),
            'closed' => Ticket::where('assigned_to_department', 'GA')->where('status', 'closed')->count(),
        ];

        return response()->json([
            'it' => $itStats,
            'ga' => $gaStats
        ]);
    }
}
