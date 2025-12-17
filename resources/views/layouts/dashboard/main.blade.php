<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('dist/css/adminx.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/css/select2.min.css') }}">

    @stack('styles')
</head>
<body>

<div class="adminx-container">

    {{-- Navbar --}}
    @includeIf('layouts.dashboard.partials.navbar')

    <div class="adminx-content">

        {{-- Sidebar --}}
        @includeIf('layouts.dashboard.partials.sidebar')

        <div class="adminx-main-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('dist/js/vendor.js') }}"></script>
<script src="{{ asset('dist/js/adminx.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@stack('scripts')

</body>
</html>
