@extends('layouts.app')

@section('title', 'My Tickets')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">My Tickets</h2>
        <a href="{{ url('/user/tickets/create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Create New Ticket
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="{{ url('/user/tickets') }}" method="GET" class="row g-3">
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search tickets..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
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
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets ?? [] as $ticket)
                        <tr>
                            <td>
                                <a href="{{ url('/user/tickets/'.$ticket->id) }}" class="text-decoration-none">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </td>
                            <td>{{ Str::limit($ticket->subject, 40) }}</td>
                            <td>{{ $ticket->category->name }}</td>
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
                            <td>{{ $ticket->updated_at->diffForHumans() }}</td>
                            <td>
                                <a href="{{ url('/user/tickets/'.$ticket->id) }}" class="btn btn-sm btn-outline-primary">
                                    View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
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
        @if(isset($tickets) && $tickets->hasPages())
        <div class="card-footer">
            {{ $tickets->appends(request()->except('page'))->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
