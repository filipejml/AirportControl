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

                <!-- Links para Administrador -->
                @if(auth()->user()->tipo == 0)
                    <!-- Companhias Aéreas com Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('companhias.*') ? 'active' : '' }}" 
                           href="#" 
                           id="companhiasDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            <i class="bi bi-building me-1"></i>Companhias Aéreas
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="companhiasDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('companhias.informacoes') ? 'active' : '' }}" 
                                   href="{{ route('companhias.informacoes') }}">
                                    <i class="bi bi-info-circle me-2"></i>Informações Gerais
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('companhias.index') ? 'active' : '' }}" 
                                   href="{{ route('companhias.index') }}">
                                    <i class="bi bi-list-ul me-2"></i>Gerenciar Companhias
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Aeronaves com Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('aeronaves.*') ? 'active' : '' }}" 
                        href="#" 
                        id="aeronavesDropdown" 
                        role="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                            <i class="bi bi-airplane me-1"></i>Aeronaves
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="aeronavesDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('aeronaves.informacoes') ? 'active' : '' }}" 
                                href="{{ route('aeronaves.informacoes') }}">
                                    <i class="bi bi-info-circle me-2"></i>Informações Gerais
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('aeronaves.ranking') ? 'active' : '' }}" 
                                href="{{ route('aeronaves.ranking') }}">
                                    <i class="bi bi-trophy me-2"></i>Ranking de Aeronaves
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('aeronaves.index') ? 'active' : '' }}" 
                                href="{{ route('aeronaves.index') }}">
                                    <i class="bi bi-list-ul me-2"></i>Gerenciar Aeronaves
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Aeroportos com Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('aeroportos.*') ? 'active' : '' }}" 
                           href="#" 
                           id="aeroportosDropdown" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            <i class="bi bi-geo-alt me-1"></i>Aeroportos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="aeroportosDropdown">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('aeroportos.informacoes') ? 'active' : '' }}" 
                                   href="{{ route('aeroportos.informacoes') }}">
                                    <i class="bi bi-info-circle me-2"></i>Informações Gerais
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('aeroportos.index') ? 'active' : '' }}" 
                                   href="{{ route('aeroportos.index') }}">
                                    <i class="bi bi-list-ul me-2"></i>Gerenciar Aeroportos
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <!-- Links para Usuário Comum -->
                    <!-- Companhias Aéreas (apenas informações gerais) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('companhias.informacoes') ? 'active' : '' }}" 
                           href="{{ route('companhias.informacoes') }}">
                            <i class="bi bi-building me-1"></i>Companhias Aéreas
                        </a>
                    </li>
                    
                    <!-- Aeronaves (apenas informações gerais) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('companhias.informacoes') ? 'active' : '' }}" 
                        href="{{ route('companhias.informacoes') }}">
                            <i class="bi bi-building me-1"></i>Companhias Aéreas
                        </a>
                    </li>

                    <!-- Aeronaves (apenas informações gerais e ranking) -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('aeronaves.*') ? 'active' : '' }}" 
                        href="#" 
                        id="aeronavesDropdownUser" 
                        role="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                            <i class="bi bi-airplane me-1"></i>Aeronaves
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="aeronavesDropdownUser">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('aeronaves.informacoes') ? 'active' : '' }}" 
                                href="{{ route('aeronaves.informacoes') }}">
                                    <i class="bi bi-info-circle me-2"></i>Informações Gerais
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('aeronaves.ranking') ? 'active' : '' }}" 
                                href="{{ route('aeronaves.ranking') }}">
                                    <i class="bi bi-trophy me-2"></i>Ranking de Aeronaves
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Aeroportos (apenas informações gerais) -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('aeroportos.informacoes') ? 'active' : '' }}" 
                           href="{{ route('aeroportos.informacoes') }}">
                            <i class="bi bi-geo-alt me-1"></i>Aeroportos
                        </a>
                    </li>
                @endif

                <!-- 📊 DASHBOARD com Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('dashboard.*') ? 'active' : '' }}" 
                    href="#" 
                    id="dashboardDropdown" 
                    role="button" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                        <i class="bi bi-bar-chart me-1"></i>Dashboard
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dashboardDropdown">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                            href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>Painel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('dashboard.graficos') ? 'active' : '' }}" 
                            href="{{ route('dashboard.graficos') }}">
                                <i class="bi bi-graph-up me-2"></i>Gráficos
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Relatórios - Visível para todos, mas com controle de acesso -->
                @auth
                    @if(auth()->user()->tipo == 0) {{-- Admin --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="relatoriosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                📊 Relatórios
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="relatoriosDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('relatorios') }}">
                                        📈 Relatórios Disponíveis
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.relatorios.index') }}">
                                        ⚙️ Controle de Relatórios
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else {{-- Usuário comum --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('relatorios') }}">
                                📊 Relatórios
                            </a>
                        </li>
                    @endif
                @endauth

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