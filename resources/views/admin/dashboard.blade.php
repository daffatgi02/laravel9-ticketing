@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Administrator Dashboard</h2>
            <div>
                <a href="{{ url('/admin/reports') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-file-alt me-1"></i> Reports
                </a>
                <a href="{{ url('/admin/tickets') }}" class="btn btn-primary">
                    <i class="fas fa-ticket-alt me-1"></i> Manage Tickets
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="display-4 mb-2">{{ $stats['total_tickets'] ?? 0 }}</div>
                        <div class="text-muted">Total Tickets</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info bg-opacity-10 h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="display-4 mb-2">{{ $stats['new_tickets'] ?? 0 }}</div>
                        <div class="text-muted">New Tickets</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-warning bg-opacity-10 h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="display-4 mb-2">{{ $stats['in_progress_tickets'] ?? 0 }}</div>
                        <div class="text-muted">In Progress</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success bg-opacity-10 h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="display-4 mb-2">{{ $stats['resolved_tickets'] ?? 0 }}</div>
                        <div class="text-muted">Resolved</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-ticket-alt me-2"></i> Tickets Requiring Attention</span>
                        <a href="{{ url('/admin/tickets?status=new') }}" class="btn btn-sm btn-outline-primary">View All
                            New</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ticket #</th>
                                        <th>Subject</th>
                                        <th>Requester</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stats['recent_tickets'] ?? [] as $ticket)
                                        <tr>
                                            <td>
                                                <a href="{{ url('/admin/tickets/' . $ticket->id) }}"
                                                    class="text-decoration-none">
                                                    {{ $ticket->ticket_number }}
                                                </a>
                                            </td>
                                            <td>{{ Str::limit($ticket->subject, 30) }}</td>
                                            <td>{{ $ticket->user->name }}</td>
                                            <td>{{ $ticket->category->name }}</td>
                                            <td>
                                                <span class="badge badge-ticket badge-{{ $ticket->status }}">
                                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
                                                        Action
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item"
                                                                href="{{ url('/admin/tickets/' . $ticket->id) }}">View
                                                                Details</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ url('/admin/tickets/' . $ticket->id . '/assign') }}">Assign</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">No tickets requiring attention</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <i class="fas fa-chart-line me-2"></i> Ticket Trend (Last 12 Months)
                    </div>
                    <div class="card-body">
                        <canvas id="ticketTrendChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-tasks me-2"></i> Ticket Distribution
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="d-block mb-1">IT Department</span>
                            <div class="progress" style="height: 10px;">
                                @php
                                    $itPercentage =
                                        $stats['total_tickets'] > 0
                                            ? ($stats['it_tickets'] / $stats['total_tickets']) * 100
                                            : 0;
                                @endphp
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $itPercentage }}%;" aria-valuenow="{{ $itPercentage }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ round($itPercentage) }}%
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">{{ $stats['it_tickets'] ?? 0 }} Tickets</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="d-block mb-1">GA Department</span>
                            <div class="progress" style="height: 10px;">
                                @php
                                    $gaPercentage =
                                        $stats['total_tickets'] > 0
                                            ? ($stats['ga_tickets'] / $stats['total_tickets']) * 100
                                            : 0;
                                @endphp
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $gaPercentage }}%;" aria-valuenow="{{ $gaPercentage }}"
                                    aria-valuemin="0" aria-valuemax="100">
                                    {{ round($gaPercentage) }}%
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">{{ $stats['ga_tickets'] ?? 0 }} Tickets</small>
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">By Priority</h6>
                        <div class="mb-2">
                            <span class="d-flex justify-content-between mb-1">
                                <span>Low</span>
                                <span>{{ $stats['ticket_by_priority']['low'] ?? 0 }}</span>
                            </span>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info"
                                    style="width: {{ (($stats['ticket_by_priority']['low'] ?? 0) / ($stats['total_tickets'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="d-flex justify-content-between mb-1">
                                <span>Medium</span>
                                <span>{{ $stats['ticket_by_priority']['medium'] ?? 0 }}</span>
                            </span>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                    style="width: {{ (($stats['ticket_by_priority']['medium'] ?? 0) / ($stats['total_tickets'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="d-flex justify-content-between mb-1">
                                <span>High</span>
                                <span>{{ $stats['ticket_by_priority']['high'] ?? 0 }}</span>
                            </span>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning"
                                    style="width: {{ (($stats['ticket_by_priority']['high'] ?? 0) / ($stats['total_tickets'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="d-flex justify-content-between mb-1">
                                <span>Urgent</span>
                                <span>{{ $stats['ticket_by_priority']['urgent'] ?? 0 }}</span>
                            </span>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger"
                                    style="width: {{ (($stats['ticket_by_priority']['urgent'] ?? 0) / ($stats['total_tickets'] ?? 1)) * 100 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-users me-2"></i> User Stats
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>Total Users</div>
                            <div><strong>{{ $stats['users_count']['total'] ?? 0 }}</strong></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>Active Users</div>
                            <div><strong>{{ $stats['users_count']['active'] ?? 0 }}</strong></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>IT Support</div>
                            <div><strong>{{ $stats['users_count']['it'] ?? 0 }}</strong></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>GA Support</div>
                            <div><strong>{{ $stats['users_count']['ga'] ?? 0 }}</strong></div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>Regular Users</div>
                            <div><strong>{{ $stats['users_count']['users'] ?? 0 }}</strong></div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ url('/admin/users') }}" class="btn btn-sm btn-outline-primary w-100">
                                Manage Users <i class="fas fa-arrow-right ms-1"></i>
                            </a>
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
            const monthlyData = @json($stats['monthly_tickets'] ?? []);

            if (monthlyData.length) {
                const labels = monthlyData.map(item => {
                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep",
                        "Oct", "Nov", "Dec"
                    ];
                    return monthNames[item.month - 1] + ' ' + item.year;
                }).reverse();

                const counts = monthlyData.map(item => item.count).reverse();

                const ctx = document.getElementById('ticketTrendChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Tickets',
                            data: counts,
                            backgroundColor: 'rgba(52, 152, 219, 0.2)',
                            borderColor: 'rgba(52, 152, 219, 1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
