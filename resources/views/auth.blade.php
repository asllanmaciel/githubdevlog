<x-layout :title="$mode === 'register' ? 'Criar workspace - GitHub DevLog AI' : 'Entrar - GitHub DevLog AI'">
  <div class="row justify-content-center py-4">
    <div class="col-lg-6">
      <div class="cardx">
        <div class="kicker">{{ $mode === 'register' ? 'Novo workspace' : 'Acesso ao painel' }}</div>
        <h1 class="h2 mt-2">{{ $mode === 'register' ? 'Crie sua base privada de webhooks GitHub.' : 'Entre no seu workspace.' }}</h1>
        <form class="mt-4" method="POST" action="{{ $mode === 'register' ? route('register.store') : route('login.store') }}">
          @csrf
          @if ($mode === 'register')
            <div class="mb-3"><label>Nome</label><input name="name" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label>Workspace</label><input name="workspace" value="{{ old('workspace') }}" placeholder="Ex: Time Plataforma" required></div>
          @endif
          <div class="mb-3"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
          <div class="mb-3"><label>Senha</label><input type="password" name="password" required minlength="{{ $mode === 'register' ? 8 : 1 }}"></div>
          <button class="btnx primary w-100" type="submit">{{ $mode === 'register' ? 'Criar workspace' : 'Entrar' }}</button>
        </form>
        <p class="muted mt-3 mb-0">
          @if ($mode === 'register')
            Já tem conta? <a href="{{ route('login') }}">Entrar no painel</a>.
          @else
            Ainda não tem conta? <a href="{{ route('register') }}">Criar workspace</a>.
          @endif
        </p>
      </div>
    </div>
  </div>
</x-layout>
