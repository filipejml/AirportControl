<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // Tela para solicitar recuperação de senha
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // Processar solicitação de recuperação
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Não encontramos um usuário com este e-mail.'
        ]);

        $user = User::where('email', $request->email)->first();
        
        // Gerar token único
        $token = Str::random(60);
        
        // Salvar token e expiração (válido por 1 hora)
        $user->reset_token = $token;
        $user->reset_token_expires_at = Carbon::now()->addHour();
        $user->save();

        // Enviar e-mail com link de redefinição
        // Nota: Você precisa configurar o envio de e-mail no .env
        // Para teste local, você pode usar Mailtrap ou log
        
        try {
            Mail::send('auth.emails.reset-password', ['token' => $token, 'user' => $user], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Recuperação de Senha - Seu Sistema');
            });
            
            return back()->with('success', 'Enviamos um link de recuperação para seu e-mail!');
        } catch (\Exception $e) {
            // Se não conseguir enviar e-mail, mostra o link para teste
            $resetUrl = route('password.reset', ['token' => $token]);
            return back()->with('info', "Link de recuperação (modo desenvolvimento): <a href='{$resetUrl}'>Clique aqui para redefinir sua senha</a>");
        }
    }

    // Tela para redefinir senha
    public function showResetForm($token)
    {
        $user = User::where('reset_token', $token)
                    ->where('reset_token_expires_at', '>', Carbon::now())
                    ->first();
        
        if (!$user) {
            return redirect()->route('password.request')
                ->with('error', 'Link inválido ou expirado. Solicite uma nova recuperação.');
        }
        
        return view('auth.reset-password', ['token' => $token]);
    }

    // Processar redefinição de senha
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);
        
        $user = User::where('reset_token', $request->token)
                    ->where('reset_token_expires_at', '>', Carbon::now())
                    ->first();
        
        if (!$user) {
            return redirect()->route('password.request')
                ->with('error', 'Link inválido ou expirado. Solicite uma nova recuperação.');
        }
        
        // Atualizar senha
        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->reset_token_expires_at = null;
        $user->save();
        
        return redirect()->route('login')
            ->with('success', 'Senha redefinida com sucesso! Faça login com sua nova senha.');
    }
}