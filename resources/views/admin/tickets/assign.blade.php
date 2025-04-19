@extends('layouts.app')

@section('title', 'Assign Ticket')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Assign Ticket #{{ $ticket->ticket_number }}</h2>
        <a href="{{ url('/admin/tickets/'.$ticket->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Ticket
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-user-check me-2"></i> Assign Ticket
                </div>
                <div class="card-body">
                    <form action="{{ url('/hc/tickets/'.$ticket->id.'/assign') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <h5 class="mb-3">Ticket Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-3 text-muted">Subject:</div>
                                <div class="col-md-9"><strong>{{ $ticket->subject }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3 text-muted">Category:</div>
                                <div class="col-md-9"><strong>{{ $ticket->category->name }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3 text-muted">Requester:</div>
                                <div class="col-md-9"><strong>{{ $ticket->user->name }}</strong></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-3 text-muted">Priority:</div>
                                <div class="col-md-9">
                                    @if($ticket->priority == 'low')
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
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h5 class="mb-3">Assignment Details</h5>

                            <div class="mb-3">
                                <label for="assigned_to_department" class="form-label">Assign to Department <span class="text-danger">*</span></label>
                                <select id="assigned_to_department" name="assigned_to_department" class="form-select @error('assigned_to_department') is-invalid @enderror" required>
                                    <option value="">Select Department</option>
                                    <option value="IT" {{ old('assigned_to_department', $ticket->category->assigned_to) == 'IT' ? 'selected' : '' }}>IT Department</option>
                                    <option value="GA" {{ old('assigned_to_department', $ticket->category->assigned_to) == 'GA' ? 'selected' : '' }}>GA Department</option>
                                </select>
                                @error('assigned_to_department')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Based on the ticket category, the recommended department is {{ $ticket->category->assigned_to ?? 'not specified' }}.</small>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Assignment Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                        id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                <small class="text-muted">Optional notes for the support team.</small>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ url('/admin/tickets/'.$ticket->id) }}" class="btn btn-outline-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-check me-1"></i> Assign Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Description
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="mb-2">Issue Description:</h6>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($ticket->description)) !!}
                        </div>
                    </div>

                    @if($ticket->attachments->count() > 0)
                    <div>
                        <h6 class="mb-2">Attachments:</h6>
                        <div class="list-group">
                            @foreach($ticket->attachments as $attachment)
                            <a href="{{ asset('storage/'.$attachment->path) }}" class="list-group-item list-group-item-action" target="_blank">
                                <div class="d-flex align-items-center">
                                    @if(in_array($attachment->mime_type, ['image/jpeg', 'image/png', 'image/jpg']))
                                        <i class="fas fa-file-image text-primary me-2"></i>
                                    @elseif($attachment->mime_type == 'application/pdf')
                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                    @else
                                        <i class="fas fa-file text-secondary me-2"></i>
                                    @endif
                                    <div>
                                        <div>{{ $attachment->filename }}</div>
                                        <small class="text-muted">{{ round($attachment->size / 1024, 2) }} KB</small>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
