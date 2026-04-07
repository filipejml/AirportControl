<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            height: 100vh;
        }
        .reset-card {
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
    <div class="card reset-card shadow p-4" style="width: 100%; max-width: 400px;">
        
        <div class="text-center mb-4">
            <h3 class="fw-bold">🔐 Redefinir Senha</h3>
            <p class="text-muted">Crie uma nova senha para sua conta</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label class="form-label">Nova Senha</label>
                <input type="password" 
                       name="password" 
                       class="form-control" 
                       placeholder="Digite sua nova senha (mínimo 6 caracteres)"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar Nova Senha</label>
                <input type="password" 
                       name="password_confirmation" 
                       class="form-control" 
                       placeholder="Confirme sua nova senha"
                       required>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">
                    Redefinir Senha
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-decoration-none">
                    Voltar para o login
                </a>
            </div>
        </form>
    </div>
</div>
</body>
</html>