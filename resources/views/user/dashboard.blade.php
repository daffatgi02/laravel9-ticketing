@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">My Dashboard</h2>
        <a href="{{ url('/user/tickets/create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Create New Ticket
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center h-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="display-4 mb-2">{{ $stats['my_tickets']['total'] ?? 0 }}</div>
                            <div class="text-muted">Total Tickets</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center bg-info bg-opacity-10 h-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="display-4 mb-2">{{ $stats['my_tickets']['new'] ?? 0 }}</div>
                            <div class="text-muted">New</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center bg-warning bg-opacity-10 h-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="display-4 mb-2">{{ $stats['my_tickets']['in_progress'] ?? 0 }}</div>
                            <div class="text-muted">In Progress</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center bg-success bg-opacity-10 h-100">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="display-4 mb-2">{{ $stats['my_tickets']['resolved'] ?? 0 }}</div>
                            <div class="text-muted">Resolved</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-ticket-alt me-2"></i> Recent Tickets</span>
                    <a href="{{ url('/user/tickets') }}" class="btn btn-sm btn-outline-secondary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Subject</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stats['recent_tickets'] ?? [] as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ url('/user/tickets/'.$ticket->id) }}" class="text-decoration-none">
                                            {{ $ticket->ticket_number }}
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($ticket->subject, 40) }}</td>
                                    <td>{{ $ticket->category->name }}</td>
                                    <td>
                                        <span class="badge badge-ticket badge-{{ $ticket->status }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">No tickets found</div>
                                        <a href="{{ url('/user/tickets/create') }}" class="btn btn-sm btn-primary mt-2">
                                            Create Your First Ticket
                                        </a>
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
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Quick Help
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <span class="bg-primary text-white p-3 rounded-circle">
                                <i class="fas fa-question"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-1">Need Help?</h5>
                            <p class="mb-0 text-muted">Submit a ticket for any technical or facility issues</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3">
                            <span class="bg-success text-white p-3 rounded-circle">
                                <i class="fas fa-clock"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-1">Track Progress</h5>
                            <p class="mb-0 text-muted">Follow the status of your tickets in real-time</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <span class="bg-info text-white p-3 rounded-circle">
                                <i class="fas fa-comments"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-1">Stay Connected</h5>
                            <p class="mb-0 text-muted">Communicate directly with support staff</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
