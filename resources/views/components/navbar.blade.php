<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d5c8b;">
    <div class="container-fluid">

        <!-- Logo / Nome -->
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            📊 Monitoramento UESPI
        </a>

        <!-- Botão mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Conteúdo -->
        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- Menu -->
            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="#">Tela Inicial</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Cadastro de Voos</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Lista Geral de Voos</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Companhias Aéreas</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Dashboard</a>
                </li>

                <!-- SOMENTE ADMIN -->
                @if(auth()->user()->tipo == 0)
                    <li class="nav-item">
                        <a class="nav-link" href="#">Registros</a>
                    </li>
                @endif

            </ul>

            <!-- Usuário -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        👤 {{ auth()->user()->username }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">Sair</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</nav>