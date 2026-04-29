<x-layout title="Convite de workspace | GitHub DevLog AI">
  <main class="hero">
    <span class="eyebrow">Convite de workspace</span>
    <h1>Entre no workspace {{ $invite->workspace?->name }}.</h1>
    <p class="lead">Voce foi convidado para colaborar no GitHub DevLog AI como <strong>{{ $roleLabel }}</strong>. O acesso e privado por workspace: voce ve apenas os webhooks, tarefas e historico deste ambiente.</p>

    <div class="hero-grid">
      <section class="panel">
        <div class="kicker">Dados do convite</div>
        <div class="signal"><strong>Email</strong><span>{{ $invite->email }}</span></div>
        <div class="signal"><strong>Workspace</strong><span>{{ $invite->workspace?->name }}</span></div>
        <div class="signal"><strong>Status</strong><span>{{ $invite->status }}</span></div>
        <div class="signal"><strong>Expira</strong><span>{{ $invite->expires_at?->format('d/m/Y H:i') ?? 'Sem expiracao definida' }}</span></div>
      </section>

      <section class="panel">
        <div class="kicker">Proximo passo</div>
        @auth
          @if (strtolower(auth()->user()->email) === strtolower($invite->email) && $invite->isAcceptable())
            <h2 class="h3">Aceitar convite</h2>
            <p class="muted">Ao aceitar, seu usuario sera vinculado ao workspace com o papel definido pelo owner/admin.</p>
            <form method="POST" action="{{ route('workspace.invites.accept', $invite->token) }}">
              @csrf
              <button class="btnx primary" type="submit">Aceitar convite</button>
            </form>
          @elseif (! $invite->isAcceptable())
            <h2 class="h3">Convite indisponivel</h2>
            <p class="muted">Este convite ja foi usado, cancelado ou expirou. Peca um novo convite ao owner do workspace.</p>
          @else
            <h2 class="h3">Email diferente</h2>
            <p class="muted">Voce esta logado como {{ auth()->user()->email }}. Para aceitar, entre com {{ $invite->email }}.</p>
          @endif
        @else
          <h2 class="h3">Entre ou crie conta</h2>
          <p class="muted">Use o mesmo e-mail do convite. Se criar uma nova conta com esse e-mail, o workspace sera vinculado automaticamente.</p>
          <div class="d-flex gap-2 flex-wrap mt-3">
            <a class="btnx primary" href="{{ route('register') }}">Criar conta</a>
            <a class="btnx" href="{{ route('login') }}">Entrar</a>
          </div>
        @endauth
      </section>
    </div>
  </main>
</x-layout>