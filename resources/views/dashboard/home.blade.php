<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Monitoramento UESPI</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Ícones -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f5f7fa;
        }

        .card-dashboard {
            border-radius: 12px;
            transition: 0.3s;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('components.navbar')

    <!-- Conteúdo -->
    <div class="container mt-4">

        <!-- Cabeçalho -->
        <div class="mb-4">
            <h3 class="fw-bold">
                Bem-vindo, {{ auth()->user()->name }}
            </h3>
            <p class="text-muted">
                {{ auth()->user()->tipo == 0 ? 'Administrador' : 'Usuário comum' }}
            </p>
        </div>

        <!-- Cards do sistema -->
        <div class="row g-4">

            <div class="col-md-4">
                <div class="card card-dashboard shadow-sm p-3">
                    <h5><i class="bi bi-airplane"></i> Cadastro de Voos</h5>
                    <p class="text-muted">Cadastrar novos voos no sistema</p>
                    <a href="#" class="btn btn-primary btn-sm">Acessar</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-dashboard shadow-sm p-3">
                    <h5><i class="bi bi-list"></i> Lista de Voos</h5>
                    <p class="text-muted">Visualizar todos os voos</p>
                    <a href="#" class="btn btn-primary btn-sm">Acessar</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-dashboard shadow-sm p-3">
                    <h5><i class="bi bi-building"></i> Companhias</h5>
                    <p class="text-muted">Gerenciar companhias aéreas</p>
                    <a href="#" class="btn btn-primary btn-sm">Acessar</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-dashboard shadow-sm p-3">
                    <h5><i class="bi bi-bar-chart"></i> Dashboard</h5>
                    <p class="text-muted">Análises e relatórios</p>
                    <a href="#" class="btn btn-primary btn-sm">Acessar</a>
                </div>
            </div>

            <!-- SOMENTE ADMIN -->
            @if(auth()->user()->tipo == 0)
            <div class="col-md-4">
                <div class="card card-dashboard shadow-sm p-3 border-danger">
                    <h5 class="text-danger">
                        <i class="bi bi-shield-lock"></i> Registros
                    </h5>
                    <p class="text-muted">Logs e atividades do sistema</p>
                    <a href="#" class="btn btn-danger btn-sm">Acessar</a>
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>