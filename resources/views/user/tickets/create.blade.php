@extends('layouts.app')

@section('title', 'Create New Ticket')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Create New Ticket</h2>
        <a href="{{ url('/user/tickets') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Tickets
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <i class="fas fa-plus-circle me-2"></i> Submit a New Support Request
        </div>
        <div class="card-body">
            <form action="{{ url('/user/tickets') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select a category</option>
                            @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} - {{ $category->assigned_to }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                        <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }} selected>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                        @error('priority')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror"
                           id="subject" name="subject" value="{{ old('subject') }}" required>
                    @error('subject')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                    <small class="text-muted">Please provide as much detail as possible to help us resolve your issue quickly.</small>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="attachments" class="form-label">Attachments (Optional)</label>
                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror"
                           id="attachments" name="attachments[]" multiple>
                    <small class="text-muted">You can upload up to 4 files (max 5MB each). Accepted formats: JPEG, PNG, JPG, PDF.</small>
                    @error('attachments.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back();">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i> Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
