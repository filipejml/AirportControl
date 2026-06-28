<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#071f4a">
    <title>Entrar | Airport Control</title>

    <style>
        :root {
            --navy: #071f4a;
            --blue: #0b4ea2;
            --sky: #2785d0;
            --yellow: #ffc928;
            --ink: #10213b;
            --muted: #65748a;
            --line: #dce4ee;
            --danger: #a82731;
            --success: #18734b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            background: #eaf1f8;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
        }

        button,
        input {
            font: inherit;
        }

        .login-shell {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(420px, .85fr);
            min-height: 100vh;
        }

        .airport-scene {
            position: relative;
            display: flex;
            min-height: 100vh;
            padding: 48px clamp(36px, 6vw, 92px);
            overflow: hidden;
            color: #fff;
            background:
                radial-gradient(circle at 18% 12%, rgba(255,255,255,.19), transparent 20%),
                linear-gradient(165deg, #0b3f8c 0%, #1473bf 51%, #a8dcf1 51.2%, #d8eef5 60%, #4b5d69 60.2%, #26343d 100%);
        }

        .airport-scene::before {
            content: "";
            position: absolute;
            right: -8%;
            bottom: 7%;
            width: 80%;
            height: 32%;
            opacity: .72;
            background:
                linear-gradient(89deg, transparent 48.5%, rgba(255,255,255,.7) 49%, rgba(255,255,255,.7) 51%, transparent 51.5%),
                repeating-linear-gradient(90deg, transparent 0 65px, rgba(255,255,255,.3) 66px 69px, transparent 70px 130px);
            clip-path: polygon(18% 100%, 43% 0, 58% 0, 100% 100%);
            transform: rotate(-8deg);
        }

        .airport-scene::after {
            content: "";
            position: absolute;
            right: -4%;
            top: 49%;
            width: 72%;
            height: 14%;
            border-top: 10px solid rgba(255,255,255,.95);
            border-bottom: 5px solid #f1a91d;
            background:
                repeating-linear-gradient(90deg, #e9f1f5 0 38px, #5380a0 39px 55px, #f8fbfc 56px 88px);
            box-shadow: 0 12px 25px rgba(4,25,54,.25);
            transform: perspective(400px) rotateY(-7deg);
        }

        .scene-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex: 1;
            flex-direction: column;
            justify-content: space-between;
            max-width: 720px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 13px;
            width: fit-content;
            color: inherit;
            text-decoration: none;
        }

        .brand-mark {
            display: grid;
            width: 46px;
            height: 46px;
            place-items: center;
            border: 1px solid rgba(255,255,255,.28);
            border-radius: 13px;
            background: rgba(3,28,68,.44);
            box-shadow: inset 0 1px rgba(255,255,255,.2);
            backdrop-filter: blur(8px);
        }

        .brand-mark svg {
            width: 27px;
        }

        .brand-name {
            display: block;
            font-size: 1.02rem;
            font-weight: 800;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .brand-subtitle {
            display: block;
            margin-top: 2px;
            color: rgba(255,255,255,.66);
            font-size: .68rem;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .hero-copy {
            margin-top: auto;
            margin-bottom: clamp(300px, 43vh, 430px);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 18px;
            color: var(--yellow);
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .17em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 28px;
            height: 2px;
            background: currentColor;
        }

        .hero-copy h1 {
            max-width: 640px;
            margin: 0;
            font-size: clamp(2.7rem, 5.2vw, 5.4rem);
            line-height: .96;
            letter-spacing: -.055em;
            text-shadow: 0 5px 30px rgba(0,29,71,.25);
        }

        .hero-copy p {
            max-width: 510px;
            margin: 23px 0 0;
            color: rgba(255,255,255,.78);
            font-size: clamp(.98rem, 1.25vw, 1.14rem);
            line-height: 1.65;
        }

        .plane {
            position: absolute;
            z-index: 3;
            right: 8%;
            bottom: 19%;
            width: min(58%, 480px);
            color: #f8fbff;
            filter: drop-shadow(0 17px 10px rgba(3,18,31,.32));
            transform: rotate(-5deg);
        }

        .scene-status {
            position: absolute;
            z-index: 4;
            bottom: 37px;
            left: clamp(36px, 6vw, 92px);
            display: flex;
            align-items: center;
            gap: 9px;
            color: rgba(255,255,255,.72);
            font-size: .75rem;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border: 2px solid rgba(158,255,196,.38);
            border-radius: 50%;
            background: #64df92;
            box-shadow: 0 0 0 4px rgba(100,223,146,.12);
        }

        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 54px clamp(32px, 5vw, 84px);
            background:
                linear-gradient(rgba(255,255,255,.92), rgba(255,255,255,.92)),
                repeating-linear-gradient(0deg, transparent 0 39px, rgba(8,49,98,.06) 40px);
        }

        .login-card {
            width: 100%;
            max-width: 450px;
        }

        .mobile-brand {
            display: none;
            margin-bottom: 42px;
            color: var(--navy);
        }

        .mobile-brand .brand-mark {
            color: #fff;
            background: var(--navy);
        }

        .login-kicker {
            margin: 0 0 9px;
            color: var(--blue);
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .login-card h2 {
            margin: 0;
            color: var(--navy);
            font-size: clamp(2rem, 3vw, 2.7rem);
            letter-spacing: -.045em;
        }

        .login-description {
            margin: 12px 0 34px;
            color: var(--muted);
            line-height: 1.55;
        }

        .notice {
            margin-bottom: 22px;
            padding: 13px 15px;
            border: 1px solid;
            border-radius: 10px;
            font-size: .88rem;
            line-height: 1.45;
        }

        .notice-error {
            color: var(--danger);
            border-color: #f1c7cb;
            background: #fff4f4;
        }

        .notice-success {
            color: var(--success);
            border-color: #bde4d0;
            background: #f0fbf5;
        }

        .field {
            margin-bottom: 20px;
        }

        .field-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 8px;
        }

        label {
            color: #263955;
            font-size: .83rem;
            font-weight: 700;
        }

        .forgot-link,
        .register-link {
            color: var(--blue);
            font-size: .82rem;
            font-weight: 700;
            text-decoration: none;
        }

        .forgot-link:hover,
        .register-link:hover {
            text-decoration: underline;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 16px;
            width: 19px;
            color: #78869a;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 54px;
            padding: 0 48px;
            color: var(--ink);
            border: 1px solid var(--line);
            border-radius: 10px;
            outline: none;
            background: #fff;
            transition: border-color .18s, box-shadow .18s;
        }

        .form-input::placeholder {
            color: #9aa6b5;
        }

        .form-input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(11,78,162,.1);
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            display: grid;
            width: 36px;
            height: 36px;
            padding: 0;
            place-items: center;
            color: #6b7a8f;
            border: 0;
            border-radius: 7px;
            background: transparent;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .toggle-password:hover {
            color: var(--blue);
            background: #eef4fb;
        }

        .toggle-password svg {
            width: 19px;
        }

        .submit-button {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            min-height: 56px;
            margin-top: 27px;
            padding: 0 21px;
            color: #fff;
            border: 0;
            border-radius: 10px;
            background: var(--navy);
            box-shadow: 0 12px 24px rgba(7,31,74,.2);
            cursor: pointer;
            font-weight: 800;
            transition: transform .18s, background .18s, box-shadow .18s;
        }

        .submit-button:hover {
            background: var(--blue);
            box-shadow: 0 14px 28px rgba(11,78,162,.24);
            transform: translateY(-2px);
        }

        .submit-button:focus-visible {
            outline: 3px solid rgba(11,78,162,.3);
            outline-offset: 3px;
        }

        .submit-arrow {
            font-size: 1.4rem;
            line-height: 1;
        }

        .register {
            margin: 29px 0 0;
            color: var(--muted);
            font-size: .88rem;
            text-align: center;
        }

        .flight-code {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 42px;
            color: #9aa5b4;
            font-size: .66rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .flight-code::before,
        .flight-code::after {
            content: "";
            height: 1px;
            flex: 1;
            background: var(--line);
        }

        @media (max-width: 900px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .airport-scene {
                display: none;
            }

            .form-panel {
                min-height: 100vh;
                padding: 42px 24px;
            }

            .mobile-brand {
                display: inline-flex;
            }
        }

        @media (max-width: 480px) {
            .form-panel {
                align-items: flex-start;
                padding: 28px 20px;
            }

            .mobile-brand {
                margin-bottom: 48px;
            }

            .login-description {
                margin-bottom: 28px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition: none !important;
            }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="airport-scene" aria-label="Airport Control">
            <div class="scene-content">
                <a class="brand" href="{{ url('/') }}" aria-label="Airport Control">
                    <span class="brand-mark">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M21.8 16.5 14 11.9V5.5a2 2 0 0 0-4 0v6.4l-7.8 4.6a.75.75 0 0 0-.37.65v1.2l8.17-2.56V20l-2.2 1.35V22L12 21.25 16.2 22v-.65L14 20v-4.2l8.17 2.55v-1.2a.75.75 0 0 0-.37-.65Z"/>
                        </svg>
                    </span>
                    <span>
                        <span class="brand-name">Airport Control</span>
                        <span class="brand-subtitle">Operations center</span>
                    </span>
                </a>

                <div class="hero-copy">
                    <p class="eyebrow">Centro de operações</p>
                    <h1>Seu aeroporto.<br>Seu comando.</h1>
                </div>
            </div>

            <svg class="plane" viewBox="0 0 640 250" fill="none" aria-hidden="true">
                <path fill="currentColor" d="M609 112c-32-11-142-16-226-19L245 7h-44l75 86-142 7-57-45H43l31 51-65 14c-12 3-12 20 0 23l65 13-31 52h34l57-45 142 7-75 73h44l138-73c84-3 194-8 226-19 28-10 28-29 0-39Z"/>
                <path fill="#ced7df" d="m276 93-75-86h18l110 86h-53Zm0 77-75 73h18l110-73h-53Z"/>
                <path stroke="#7b92a7" stroke-width="5" d="M97 118c121-11 340-12 504 6"/>
                <circle cx="558" cy="122" r="5" fill="#ffc928"/>
            </svg>

            <div class="scene-status">
                <span class="status-dot"></span>
                Sistema operacional
            </div>
        </section>

        <section class="form-panel">
            <div class="login-card">
                <a class="brand mobile-brand" href="{{ url('/') }}" aria-label="Airport Control">
                    <span class="brand-mark">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M21.8 16.5 14 11.9V5.5a2 2 0 0 0-4 0v6.4l-7.8 4.6a.75.75 0 0 0-.37.65v1.2l8.17-2.56V20l-2.2 1.35V22L12 21.25 16.2 22v-.65L14 20v-4.2l8.17 2.55v-1.2a.75.75 0 0 0-.37-.65Z"/>
                        </svg>
                    </span>
                    <span>
                        <span class="brand-name">Airport Control</span>
                        <span class="brand-subtitle">Operations center</span>
                    </span>
                </a>

                <p class="login-kicker">Acesso ao sistema</p>
                <h2>Bem-vindo a bordo</h2>
                <p class="login-description">Entre com suas credenciais para acessar o painel de controle.</p>

                @if ($errors->any())
                    <div class="notice notice-error" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="notice notice-success" role="status">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="field">
                        <div class="field-row">
                            <label for="login">E-mail ou usuário</label>
                        </div>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M4.5 21a7.5 7.5 0 0 1 15 0"></path>
                            </svg>
                            <input
                                id="login"
                                class="form-input"
                                type="text"
                                name="login"
                                value="{{ old('login') }}"
                                placeholder="Digite seu e-mail ou usuário"
                                autocomplete="username"
                                autofocus
                                required
                            >
                        </div>
                    </div>

                    <div class="field">
                        <div class="field-row">
                            <label for="password">Senha</label>
                            <a class="forgot-link" href="{{ route('password.request') }}">Esqueceu a senha?</a>
                        </div>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="4" y="10" width="16" height="11" rx="2"></rect>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                            </svg>
                            <input
                                id="password"
                                class="form-input"
                                type="password"
                                name="password"
                                placeholder="Digite sua senha"
                                autocomplete="current-password"
                                required
                            >
                            <button class="toggle-password" type="button" aria-label="Mostrar senha" aria-pressed="false">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="2.5"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button class="submit-button" type="submit">
                        <span>Entrar no painel</span>
                        <span class="submit-arrow" aria-hidden="true">→</span>
                    </button>
                </form>

                <p class="register">
                    Ainda não possui acesso?
                    <a class="register-link" href="{{ route('register') }}">Criar uma conta</a>
                </p>

                <div class="flight-code" aria-hidden="true">AC · Operações seguras</div>
            </div>
        </section>
    </main>

    <script>
        const togglePassword = document.querySelector('.toggle-password');
        const passwordInput = document.querySelector('#password');

        togglePassword.addEventListener('click', () => {
            const isVisible = passwordInput.type === 'text';
            passwordInput.type = isVisible ? 'password' : 'text';
            togglePassword.setAttribute('aria-pressed', String(!isVisible));
            togglePassword.setAttribute('aria-label', isVisible ? 'Mostrar senha' : 'Ocultar senha');
        });
    </script>
</body>
</html>
