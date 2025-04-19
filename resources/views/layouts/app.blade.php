<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'WIG Ticketing') }} - @yield('title', 'Support Portal')</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Nunito', sans-serif;
        }

        .sidebar {
            background-color: #2c3e50;
            min-height: 100vh;
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
            border-radius: 0.25rem;
        }

        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: white;
            background-color: #3498db;
        }

        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }

        .content-wrapper {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            padding: 1.5rem;
        }

        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1rem;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            font-weight: 600;
        }

        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }

        .navbar-brand {
            font-weight: 700;
        }

        .navbar {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .badge-ticket {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        .badge-new {
            background-color: #3498db;
            color: white;
        }

        .badge-assigned {
            background-color: #f39c12;
            color: white;
        }

        .badge-in_progress {
            background-color: #9b59b6;
            color: white;
        }

        .badge-resolved {
            background-color: #2ecc71;
            color: white;
        }

        .badge-closed {
            background-color: #7f8c8d;
            color: white;
        }

        .badge-rejected {
            background-color: #e74c3c;
            color: white;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #3498db;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .chat-message {
            margin-bottom: 1.5rem;
        }

        .chat-bubble {
            padding: 1rem;
            border-radius: 0.5rem;
            position: relative;
            max-width: 80%;
        }

        .chat-bubble.user {
            background-color: #f8f9fa;
            margin-left: auto;
            border-bottom-right-radius: 0;
        }

        .chat-bubble.other {
            background-color: #e3f2fd;
            margin-right: auto;
            border-bottom-left-radius: 0;
        }

        .chat-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>

    @yield('styles')
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'WIG Ticketing') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#">
                                        <i class="fas fa-user-circle me-2"></i> My Profile
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @auth
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2 sidebar py-4">
                        @if (Auth::user()->isAdmin())
                            <!-- HC Admin Sidebar -->
                            <h6 class="text-uppercase px-3 mt-2 mb-3 text-muted">Administration</h6>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}"
                                        href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('admin/tickets*') ? 'active' : '' }}"
                                        href="{{ route('admin.tickets.index') }}">
                                        <i class="fas fa-ticket-alt"></i> All Tickets
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}"
                                        href="{{ route('admin.users.index') }}">
                                        <i class="fas fa-users"></i> Users
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('admin/departments*') ? 'active' : '' }}"
                                        href="{{ route('admin.departments.index') }}">
                                        <i class="fas fa-building"></i> Departments
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}"
                                        href="{{ route('admin.categories.index') }}">
                                        <i class="fas fa-tags"></i> Categories
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('admin/reports*') ? 'active' : '' }}"
                                        href="{{ route('admin.reports') }}">
                                        <i class="fas fa-chart-bar"></i> Reports
                                    </a>
                                </li>
                            </ul>
                        @elseif(Auth::user()->isIT() || Auth::user()->isGA())
                            <!-- Support Staff Sidebar -->
                            <h6 class="text-uppercase px-3 mt-2 mb-3 text-muted">Support Portal</h6>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('support/dashboard*') ? 'active' : '' }}"
                                        href="{{ route('support.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('support/tickets*') && !request()->is('*/assigned') ? 'active' : '' }}"
                                        href="{{ route('support.tickets.index') }}">
                                        <i class="fas fa-ticket-alt"></i> All Tickets
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('*/assigned') ? 'active' : '' }}"
                                        href="{{ route('support.tickets.assigned') }}">
                                        <i class="fas fa-tasks"></i> My Assigned
                                    </a>
                                </li>
                            </ul>
                        @else
                            <!-- Regular User Sidebar -->
                            <h6 class="text-uppercase px-3 mt-2 mb-3 text-muted">My Tickets</h6>
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('user/dashboard*') ? 'active' : '' }}"
                                        href="{{ route('user.dashboard') }}">
                                        <i class="fas fa-tachometer-alt"></i> Dashboard
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('user/tickets*') && !request()->is('*/create') ? 'active' : '' }}"
                                        href="{{ route('user.tickets.index') }}">
                                        <i class="fas fa-ticket-alt"></i> My Tickets
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->is('*/create') ? 'active' : '' }}"
                                        href="{{ route('user.tickets.create') }}">
                                        <i class="fas fa-plus-circle"></i> New Ticket
                                    </a>
                                </li>
                            </ul>
                        @endif
                    </div>
                    <div class="col-md-10 py-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                </div>
            </div>
        @else
            <main class="py-4">
                @yield('content')
            </main>
        @endauth
    </div>

    @yield('scripts')
</body>

</html>
