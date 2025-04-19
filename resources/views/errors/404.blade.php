@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i class="fas fa-search text-primary" style="font-size: 5rem;"></i>
            </div>
            <h1 class="mb-4">Page Not Found</h1>
            <div class="card">
                <div class="card-body py-5">
                    <h4 class="mb-4">Oops! The page you're looking for doesn't exist.</h4>
                    <p class="text-muted mb-4">It might have been moved or deleted.</p>

                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i> Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
