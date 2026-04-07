<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Recuperação de Senha</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #0d6efd;">Recuperação de Senha</h2>
        
        <p>Olá <strong>{{ $user->name }}</strong>,</p>
        
        <p>Recebemos uma solicitação para redefinir sua senha. Clique no botão abaixo para criar uma nova senha:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('password.reset', ['token' => $token]) }}" 
               style="background-color: #0d6efd; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                Redefinir Minha Senha
            </a>
        </div>
        
        <p>Se você não solicitou essa alteração, ignore este e-mail. Nenhuma alteração será feita.</p>
        
        <p>Este link é válido por 1 hora.</p>
        
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
        
        <p style="font-size: 12px; color: #777;">
            Se o botão não funcionar, copie e cole o link abaixo no seu navegador:<br>
            {{ route('password.reset', ['token' => $token]) }}
        </p>
    </div>
</body>
</html>