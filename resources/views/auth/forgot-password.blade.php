<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha</title>
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
            <h3 class="fw-bold">🔐 Recuperar Senha</h3>
            <p class="text-muted">Digite seu e-mail para receber o link de recuperação</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                {!! session('info') !!}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" 
                       name="email" 
                       class="form-control" 
                       placeholder="Digite seu e-mail cadastrado"
                       value="{{ old('email') }}"
                       required>
            </div>

            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">
                    Enviar Link de Recuperação
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