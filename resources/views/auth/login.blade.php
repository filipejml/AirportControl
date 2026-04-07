<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            height: 100vh;
        }

        .login-card {
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
    <div class="card login-card shadow p-4" style="width: 100%; max-width: 400px;">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold">🔐 Login</h3>
            <p class="text-muted">Acesse sua conta</p>
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

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email ou Usuário</label>
                <input 
                    type="text" 
                    name="login" 
                    class="form-control" 
                    placeholder="Digite seu email ou usuário"
                    required
                >
            </div>

            <!-- Senha e Esqueci minha senha -->
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-control" placeholder="Digite sua senha" required>
                <div class="text-end mt-2">
                    <a href="{{ route('password.request') }}" class="text-decoration-none small">
                        Esqueceu sua senha?
                    </a>
                </div>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">
                    Entrar
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('register') }}" class="text-decoration-none">
                    Criar conta
                </a>
            </div>
        </form>

    </div>
</div>

</body>
</html>