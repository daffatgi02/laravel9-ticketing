@extends('layouts.app')

@section('title', 'All Tickets')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">All Tickets</h2>
        <div>
            <a href="{{ url('/admin/reports/tickets') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-chart-bar me-1"></i> Generate Report
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ url('/admin/tickets') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        <option value="IT" {{ request('department') == 'IT' ? 'selected' : '' }}>IT</option>
                        <option value="GA" {{ request('department') == 'GA' ? 'selected' : '' }}>GA</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priority" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search tickets..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
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
                            <th>Dept</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets ?? [] as $ticket)
                        <tr>
                            <td>
                                <a href="{{ url('/admin/tickets/'.$ticket->id) }}" class="text-decoration-none">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </td>
                            <td>{{ Str::limit($ticket->subject, 30) }}</td>
                            <td>{{ optional($ticket->user)->name }}</td>
                            <td>{{ optional($ticket->category)->name }}</td>
                            <td>
                                @if($ticket->assigned_to_department)
                                    <span class="badge bg-secondary">{{ $ticket->assigned_to_department }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
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
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Action
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ url('/admin/tickets/'.$ticket->id) }}">View Details</a></li>
                                        @if($ticket->status == 'new')
                                        <li><a class="dropdown-item" href="{{ url('/admin/tickets/'.$ticket->id.'/assign') }}">Assign</a></li>
                                        @endif
                                        @if($ticket->status != 'closed' && $ticket->status != 'rejected')
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#closeTicketModal{{ $ticket->id }}">Close Ticket</a></li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- Close Ticket Modal -->
                                <div class="modal fade" id="closeTicketModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Close Ticket #{{ $ticket->ticket_number }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ url('/admin/tickets/'.$ticket->id.'/close') }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-body">
                                                    <p>Are you sure you want to close this ticket?</p>
                                                    <div class="mb-3">
                                                        <label for="notes" class="form-label">Closing Notes (Optional)</label>
                                                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Close Ticket</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">No tickets found</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($tickets) && $tickets->hasPages())
        <div class="card-footer">
            {{ $tickets->appends(request()->except('page'))->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
