<?php

namespace App\Http\Controllers;


use App\Models\Ticket;
use App\Models\User;
use App\Models\Department;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $stats = [];

        if ($user->isAdmin()) {
            // Admin dashboard - comprehensive stats
            $stats = [
                'total_tickets' => Ticket::count(),
                'new_tickets' => Ticket::where('status', 'new')->count(),
                'in_progress_tickets' => Ticket::where('status', 'in_progress')->count(),
                'resolved_tickets' => Ticket::where('status', 'resolved')->count(),
                'closed_tickets' => Ticket::where('status', 'closed')->count(),
                'rejected_tickets' => Ticket::where('status', 'rejected')->count(),

                'it_tickets' => Ticket::where('assigned_to_department', 'IT')->count(),
                'ga_tickets' => Ticket::where('assigned_to_department', 'GA')->count(),

                'ticket_by_priority' => [
                    'low' => Ticket::where('priority', 'low')->count(),
                    'medium' => Ticket::where('priority', 'medium')->count(),
                    'high' => Ticket::where('priority', 'high')->count(),
                    'urgent' => Ticket::where('priority', 'urgent')->count(),
                ],

                'ticket_by_category' => TicketCategory::withCount('tickets')->get(),

                'recent_tickets' => Ticket::with(['user', 'category'])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),

                'users_count' => [
                    'total' => User::count(),
                    'active' => User::where('active', true)->count(),
                    'it' => User::where('role', 'it')->count(),
                    'ga' => User::where('role', 'ga')->count(),
                    'hc' => User::where('role', 'hc')->count(),
                    'users' => User::where('role', 'user')->count(),
                ],

                'department_users' => Department::withCount('users')->get(),

                // Monthly ticket trends
                'monthly_tickets' => DB::table('tickets')
                    ->select(DB::raw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count'))
                    ->groupBy('year', 'month')
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->limit(12)
                    ->get(),
            ];
        } elseif ($user->isIT() || $user->isGA()) {
            // IT/GA dashboard - focused on their tickets
            $departmentType = $user->isIT() ? 'IT' : 'GA';

            $stats = [
                'my_tickets' => Ticket::where('assigned_to_user_id', $user->id)->count(),
                'department_tickets' => Ticket::where('assigned_to_department', $departmentType)->count(),

                'status_breakdown' => [
                    'new' => Ticket::where('assigned_to_department', $departmentType)
                            ->where('status', 'new')
                            ->count(),
                    'assigned' => Ticket::where('assigned_to_department', $departmentType)
                            ->where('status', 'assigned')
                            ->count(),
                    'in_progress' => Ticket::where('assigned_to_department', $departmentType)
                            ->where('status', 'in_progress')
                            ->count(),
                    'resolved' => Ticket::where('assigned_to_department', $departmentType)
                            ->where('status', 'resolved')
                            ->count(),
                ],

                'priority_breakdown' => [
                    'low' => Ticket::where('assigned_to_department', $departmentType)
                            ->where('priority', 'low')
                            ->count(),
                    'medium' => Ticket::where('assigned_to_department', $departmentType)
                            ->where('priority', 'medium')
                            ->count(),
                    'high' => Ticket::where('assigned_to_department', $departmentType)
                            ->where('priority', 'high')
                            ->count(),
                    'urgent' => Ticket::where('assigned_to_department', $departmentType)
                            ->where('priority', 'urgent')
                            ->count(),
                ],

                'recent_tickets' => Ticket::with(['user', 'category'])
                    ->where('assigned_to_department', $departmentType)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get(),
            ];
        } else {
            // Regular user dashboard - just their tickets
            $stats = [
                'my_tickets' => [
                    'total' => Ticket::where('user_id', $user->id)->count(),
                    'new' => Ticket::where('user_id', $user->id)->where('status', 'new')->count(),
                    'in_progress' => Ticket::where('user_id', $user->id)->where('status', 'in_progress')->count(),
                    'resolved' => Ticket::where('user_id', $user->id)->where('status', 'resolved')->count(),
                    'closed' => Ticket::where('user_id', $user->id)->where('status', 'closed')->count(),
                ],

                'recent_tickets' => Ticket::with(['category'])
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
            ];
        }

        return response()->json(['stats' => $stats]);
    }
}
