@extends('layouts.app')

@section('title', Auth::user()->isIT() ? 'IT Support Dashboard' : 'GA Support Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ Auth::user()->isIT() ? 'IT' : 'GA' }} Support Dashboard</h2>
        <a href="{{ url('/support/tickets') }}" class="btn btn-primary">
            <i class="fas fa-ticket-alt me-1"></i> View All Tickets
        </a>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="display-4 mb-2">{{ $stats['department_tickets'] ?? 0 }}</div>
                    <div class="text-muted">Department Tickets</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-success bg-opacity-10 h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="display-4 mb-2">{{ $stats['my_tickets'] ?? 0 }}</div>
                    <div class="text-muted">My Tickets</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-warning bg-opacity-10 h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="display-4 mb-2">{{ $stats['status_breakdown']['in_progress'] ?? 0 }}</div>
                    <div class="text-muted">In Progress</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-info bg-opacity-10 h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <div class="display-4 mb-2">{{ $stats['status_breakdown']['new'] ?? 0 }}</div>
                    <div class="text-muted">New/Unassigned</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-ticket-alt me-2"></i> Recent {{ Auth::user()->isIT() ? 'IT' : 'GA' }} Tickets</span>
                    <a href="{{ url('/support/tickets') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Subject</th>
                                    <th>Requester</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_tickets'] ?? [] as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ url('/support/tickets/'.$ticket->id) }}" class="text-decoration-none">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($ticket->subject, 30) }}</td>
                                    <td>{{ $ticket->user->name }}</td>
                                    <td>
                                        @if($ticket->priority == 'low')
                                            <span class="badge bg-info">Low</span>
                                        @elseif($ticket->priority == 'medium')
                                            <span class="badge bg-primary">Medium</span>
                                        @elseif($ticket->priority == 'high')
                                            <span class="badge bg-warning">High</span>
                                        @elseif($ticket->priority == 'urgent')
                                            <span class="badge bg-danger">Urgent</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-ticket badge-{{ $ticket->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <a href="{{ url('/support/tickets/'.$ticket->id) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">No tickets available</div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i> Ticket Status Breakdown
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="220"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-fire-alt me-2"></i> Priority Breakdown
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Urgent</span>
                            <span class="badge bg-danger">{{ $stats['priority_breakdown']['urgent'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" style="width: {{ ($stats['priority_breakdown']['urgent'] ?? 0) / ($stats['department_tickets'] ?? 1) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>High</span>
                            <span class="badge bg-warning">{{ $stats['priority_breakdown']['high'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: {{ ($stats['priority_breakdown']['high'] ?? 0) / ($stats['department_tickets'] ?? 1) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Medium</span>
                            <span class="badge bg-primary">{{ $stats['priority_breakdown']['medium'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: {{ ($stats['priority_breakdown']['medium'] ?? 0) / ($stats['department_tickets'] ?? 1) * 100 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Low</span>
                            <span class="badge bg-info">{{ $stats['priority_breakdown']['low'] ?? 0 }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: {{ ($stats['priority_breakdown']['low'] ?? 0) / ($stats['department_tickets'] ?? 1) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status breakdown chart
        const statusData = {
            new: {{ $stats['status_breakdown']['new'] ?? 0 }},
            assigned: {{ $stats['status_breakdown']['assigned'] ?? 0 }},
            in_progress: {{ $stats['status_breakdown']['in_progress'] ?? 0 }},
            resolved: {{ $stats['status_breakdown']['resolved'] ?? 0 }}
        };

        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['New', 'Assigned', 'In Progress', 'Resolved'],
                datasets: [{
                    data: [statusData.new, statusData.assigned, statusData.in_progress, statusData.resolved],
                    backgroundColor: ['#3498db', '#f39c12', '#9b59b6', '#2ecc71']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection
