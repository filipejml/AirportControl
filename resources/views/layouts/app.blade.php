<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Airport Manager')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .table-responsive .table {
            min-width: 760px;
        }

        .table-responsive .table.table-wide {
            min-width: 980px;
        }

        .table-responsive .table.table-xl {
            min-width: 1120px;
        }

        .table-responsive thead th {
            white-space: nowrap;
        }

        .table-responsive td,
        .table-responsive th {
            vertical-align: middle;
        }

        .table-responsive td.text-center,
        .table-responsive td.text-end,
        .table-responsive th.text-center,
        .table-responsive th.text-end {
            white-space: nowrap;
        }

        .table-responsive .btn,
        .table-responsive .badge,
        .table-responsive .form-check {
            white-space: nowrap;
        }

        @media (max-width: 575.98px) {
            .table-responsive {
                margin-left: -0.75rem;
                margin-right: -0.75rem;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .table-responsive .table {
                font-size: 0.875rem;
            }

            .table-responsive .table > :not(caption) > * > * {
                padding: 0.55rem 0.65rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>

    @auth
        @include('components.navbar')
    @endauth

    <div class="container mt-4">
        @yield('content')
    </div>

    <!-- Bootstrap JavaScript (necessário para dropdowns funcionarem) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
