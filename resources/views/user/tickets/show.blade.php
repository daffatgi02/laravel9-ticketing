@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Ticket #{{ $ticket->ticket_number }}</h2>
            <div>
                <a href="{{ url('/user/tickets') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Tickets
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Ticket details and messages -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-ticket-alt me-2"></i> {{ $ticket->subject }}
                        </span>
                        <span class="badge badge-ticket badge-{{ $ticket->status }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h6 class="mb-3">Description:</h6>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($ticket->description)) !!}
                            </div>
                        </div>

                        @if ($ticket->attachments->count() > 0)
                            <div class="mb-4">
                                <h6 class="mb-2">Attachments:</h6>
                                <div class="row">
                                    @foreach ($ticket->attachments as $attachment)
                                        <div class="col-md-3 mb-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <div class="d-flex align-items-center">
                                                        @if (in_array($attachment->mime_type, ['image/jpeg', 'image/png', 'image/jpg']))
                                                            <i class="fas fa-file-image text-primary me-2"></i>
                                                        @elseif($attachment->mime_type == 'application/pdf')
                                                            <i class="fas fa-file-pdf text-danger me-2"></i>
                                                        @else
                                                            <i class="fas fa-file text-secondary me-2"></i>
                                                        @endif
                                                        <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank"
                                                            class="text-decoration-none text-truncate">
                                                            {{ $attachment->filename }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Messages/Chat -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-comments me-2"></i> Message History
                    </div>
                    <div class="card-body">
                        <div class="chat-container">
                            @forelse($ticket->messages->sortBy('created_at') as $message)
                                <div class="chat-message">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <div class="avatar">
                                                {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <div>
                                                    <strong>{{ $message->user->name }}</strong>
                                                    <span class="text-muted ms-2">
                                                        {{ $message->user->role == 'it'
                                                            ? '(IT Support)'
                                                            : ($message->user->role == 'ga'
                                                                ? '(GA Support)'
                                                                : ($message->user->role == 'hc'
                                                                    ? '(Admin)'
                                                                    : '')) }}
                                                    </span>
                                                </div>
                                                <small
                                                    class="text-muted">{{ $message->created_at->format('M d, Y g:i A') }}</small>
                                            </div>

                                            <div
                                                class="chat-bubble {{ Auth::id() == $message->user_id ? 'user' : 'other' }}">
                                                {!! nl2br(e($message->message)) !!}
                                            </div>

                                            @if ($message->attachments->count() > 0)
                                                <div class="mt-2">
                                                    <div class="d-flex flex-wrap">
                                                        @foreach ($message->attachments as $attachment)
                                                            <div class="me-3 mb-2">
                                                                <a href="{{ asset('storage/' . $attachment->path) }}"
                                                                    target="_blank"
                                                                    class="btn btn-sm btn-outline-secondary">
                                                                    <i class="fas fa-paperclip me-1"></i>
                                                                    {{ Str::limit($attachment->filename, 15) }}
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-comment-dots fa-3x mb-3"></i>
                                    <p>No messages yet. Start the conversation by adding a reply below.</p>
                                </div>
                            @endforelse
                        </div>

                        @if ($ticket->status != 'closed' && $ticket->status != 'rejected')
                            <hr class="my-4">

                            <form action="{{ url('/user/tickets/' . $ticket->id . '/messages') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="message" class="form-label">Add Reply</label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="3"
                                        required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Add Attachments (Optional)</label>
                                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                                        id="attachments" name="attachments[]" multiple>
                                    <small class="text-muted">You can upload up to 3 files (max 5MB each).</small>
                                    @error('attachments.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-1"></i> Send Reply
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-secondary mt-4">
                                <i class="fas fa-lock me-2"></i> This ticket is {{ $ticket->status }}. No new replies can
                                be added.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Ticket Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i> Ticket Information
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <div class="text-muted">Status:</div>
                            <div>
                                <span class="badge badge-ticket badge-{{ $ticket->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div class="text-muted">Category:</div>
                            <div><strong>{{ $ticket->category->name }}</strong></div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div class="text-muted">Priority:</div>
                            <div>
                                @if ($ticket->priority == 'low')
                                    <span class="badge bg-info">Low</span>
                                @elseif($ticket->priority == 'medium')
                                    <span class="badge bg-primary">Medium</span>
                                @elseif($ticket->priority == 'high')
                                    <span class="badge bg-warning">High</span>
                                @elseif($ticket->priority == 'urgent')
                                    <span class="badge bg-danger">Urgent</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div class="text-muted">Created:</div>
                            <div><strong>{{ $ticket->created_at->format('M d, Y g:i A') }}</strong></div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div class="text-muted">Last Updated:</div>
                            <div><strong>{{ $ticket->updated_at->format('M d, Y g:i A') }}</strong></div>
                        </div>

                        @if ($ticket->assigned_to_department)
                            <div class="d-flex justify-content-between mb-3">
                                <div class="text-muted">Assigned Department:</div>
                                <div><strong>{{ $ticket->assigned_to_department }}</strong></div>
                            </div>
                        @endif

                        @if ($ticket->assignedTo)
                            <div class="d-flex justify-content-between mb-3">
                                <div class="text-muted">Assigned To:</div>
                                <div><strong>{{ $ticket->assignedTo->name }}</strong></div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ticket Timeline -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-history me-2"></i> Ticket History
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach ($ticket->statusHistory->sortByDesc('created_at') as $history)
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">
                                            {{ $history->created_at->format('M d, Y g:i A') }}
                                        </small>
                                    </div>
                                    <div>
                                        <i class="fas fa-circle fa-xs me-2 text-secondary"></i>
                                        Status changed from
                                        <span class="badge badge-{{ $history->from_status }} me-1">
                                            {{ ucfirst(str_replace('_', ' ', $history->from_status ?: 'New')) }}
                                        </span>
                                        to
                                        <span class="badge badge-{{ $history->to_status }} ms-1">
                                            {{ ucfirst(str_replace('_', ' ', $history->to_status)) }}
                                        </span>
                                    </div>
                                    @if ($history->notes)
                                        <div class="small text-muted mt-1">
                                            {{ $history->notes }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <div class="list-group-item p-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">
                                        {{ $ticket->created_at->format('M d, Y g:i A') }}
                                    </small>
                                </div>
                                <div>
                                    <i class="fas fa-plus-circle fa-xs me-2 text-success"></i>
                                    Ticket created
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
