<form method="POST" action="{{ route('register.post') }}">
    @csrf

    <input type="text" name="name" placeholder="Nome">
    <input type="text" name="username" placeholder="Usuário">
    <input type="email" name="email" placeholder="Email">
    <input type="password" name="password" placeholder="Senha">

    <button type="submit">Cadastrar</button>
</form>