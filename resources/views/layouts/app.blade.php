<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee System Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { overflow-x: hidden; }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        .nav-link { color: white; margin-bottom: 10px; }
        .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); border-radius: 5px; }
    </style>
</head>
<body>
    <div class="d-flex">
        <nav id="sidebar" class="bg-dark p-3">
            <h3 class="text-white text-center mb-4">BPDA System</h3>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.index') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i> Employees
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employees.create') }}" class="nav-link {{ request()->routeIs('employees.create') ? 'active' : '' }}">
                        <i class="bi bi-person-plus me-2"></i> Add Employee
                    </a>
                </li>
            </ul>
        </nav>

        <div class="flex-fill">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom p-3">
                <span class="navbar-brand mb-0 h1">@yield('header', 'Dashboard')</span>
            </nav>
            
            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {!! session('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>