<x-layout :title="$mode === 'register' ? __('Criar workspace - GitHub DevLog AI') : __('Entrar - GitHub DevLog AI')">
  <div class="row justify-content-center py-4">
    <div class="col-lg-6">
      <div class="cardx">
        <div class="kicker">{{ $mode === 'register' ? __('Novo workspace') : __('Acesso ao painel') }}</div>
        <h1 class="h2 mt-2">{{ $mode === 'register' ? __('Crie sua base privada de webhooks GitHub.') : __('Entre no seu workspace.') }}</h1>
        <form class="mt-4" method="POST" action="{{ $mode === 'register' ? route('register.store') : route('login.store') }}">
          @csrf
          @if ($mode === 'register')
            <div class="mb-3"><label>{{ __('Nome') }}</label><input name="name" value="{{ old('name') }}" required></div>
            <div class="mb-3"><label>Workspace</label><input name="workspace" value="{{ old('workspace') }}" placeholder="{{ __('Ex: Time Plataforma') }}" required></div>
          @endif
          <div class="mb-3"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
          <div class="mb-3"><label>{{ __('Senha') }}</label><input type="password" name="password" required minlength="{{ $mode === 'register' ? 8 : 1 }}"></div>
          <button class="btnx primary w-100" type="submit">{{ $mode === 'register' ? __('Criar workspace') : __('Entrar') }}</button>
        </form>
        <p class="muted mt-3 mb-0">
          @if ($mode === 'register')
            {!! __('Já tem conta? :link.', ['link' => '<a href="'.route('login').'">'.__('Entrar no painel').'</a>']) !!}
          @else
            {!! __('Ainda não tem conta? :link.', ['link' => '<a href="'.route('register').'">'.__('Criar workspace').'</a>']) !!}
          @endif
        </p>
      </div>
    </div>
  </div>
</x-layout>
