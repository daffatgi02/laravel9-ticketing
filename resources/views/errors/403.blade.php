@extends('layouts.app')

@section('title', 'Access Denied')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 5rem;"></i>
            </div>
            <h1 class="mb-4">Access Denied</h1>
            <div class="card">
                <div class="card-body py-5">
                    <h4 class="mb-4">You do not have permission to access this page.</h4>
                    <p class="text-muted mb-4">Please contact your administrator if you believe this is a mistake.</p>

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
