{{-- resources/views/components/navbar.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d5c8b;">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            Airport Manager
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <ul class="navbar-nav me-auto">

                <!-- Links de Voos - Visíveis para todos -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('voos.create') ? 'active' : '' }}" 
                       href="{{ route('voos.create') }}">
                        <i class="bi bi-plus-circle me-1"></i>Cadastro de Voos
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('voos.index') ? 'active' : '' }}" 
                       href="{{ route('voos.index') }}">
                        <i class="bi bi-list-ul me-1"></i>Lista Geral de Voos
                    </a>
                </li>

                <!-- Links de Admin (Companhias, Aeronaves, Aeroportos) - Apenas para tipo 0 -->
                @if(auth()->user()->tipo == 0)
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('companhias.*') ? 'active' : '' }}" 
                       href="{{ route('companhias.index') }}">
                        <i class="bi bi-building me-1"></i>Companhias Aéreas
                    </a>
                </li>
                @endif

                <!-- 📊 DASHBOARD -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="bi bi-bar-chart me-1"></i>Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('relatorios') ? 'active' : '' }}" 
                       href="{{ route('relatorios') }}">
                        <i class="bi bi-file-text me-1"></i>Relatórios
                    </a>
                </li>

                <!-- ADMIN - Registros (apenas tipo 0) -->
                @if(auth()->user()->tipo == 0)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('registros') ? 'active' : '' }}" 
                           href="{{ route('registros') }}">
                            <i class="bi bi-shield-lock me-1"></i>Registros
                        </a>
                    </li>
                @endif

            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        👤 {{ auth()->user()->name }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i>Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</nav>