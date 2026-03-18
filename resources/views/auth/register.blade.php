<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            height: 100vh;
        }

        .register-card {
            border-radius: 15px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center h-100">
    <div class="card register-card shadow p-4" style="width: 100%; max-width: 400px;">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold">📝 Criar Conta</h3>
            <p class="text-muted">Preencha os dados para se registrar</p>
        </div>

        <!-- Mensagem de erro -->
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Mensagem de sucesso -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-control" 
                    placeholder="Digite seu nome"
                    value="{{ old('name') }}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Usuário</label>
                <input 
                    type="text" 
                    name="username" 
                    class="form-control" 
                    placeholder="Escolha um nome de usuário"
                    value="{{ old('username') }}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="Digite seu email"
                    value="{{ old('email') }}"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input 
                    type="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Crie uma senha"
                    required
                >
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">
                    Cadastrar
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none">
                    Já tem uma conta? Faça login
                </a>
            </div>
        </form>

    </div>
</div>

</body>
</html>