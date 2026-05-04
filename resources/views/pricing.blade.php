<x-layout :title="__('Planos - GitHub DevLog AI')">
  <main class="hero">
    <span class="eyebrow">{{ __('Planos para devs e times') }}</span>
    <h1>{{ __('Comece pequeno, escale quando seus webhooks virarem rotina de produto.') }}</h1>
    <p class="lead">
      {{ __('O DevLog AI foi pensado para cobrar por uso real: eventos recebidos, retenção, colaboração e operação. Escolha um plano, conecte seu repositório e acompanhe tudo no workspace.') }}
    </p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="{{ route('register') }}">{{ __('Criar workspace') }}</a>
      <a class="btnx" href="{{ route('docs.users') }}">{{ __('Ver guia de uso') }}</a>
    </div>
  </main>

  <section class="band">
    <div class="kicker">{{ __('Modelo comercial') }}</div>
    <h2>{{ __('Preço conectado ao volume de eventos, não a promessas vagas.') }}</h2>
    <p class="lead">
      {{ __('Cada plano define limite mensal, retenção e recursos. Quando houver excedente, o painel deve avisar antes do bloqueio ou da cobrança adicional.') }}
    </p>

    <div class="row g-3 mt-3">
      @forelse ($plans as $plan)
        @php
          $price = ((int) $plan->price_cents) / 100;
          $overage = ((int) ($plan->overage_price_cents ?? 0)) / 100;
          $features = is_array($plan->features) ? $plan->features : [];
          $isFree = $price <= 0;
          $isTestPlan = $plan->slug === 'teste-mp';
        @endphp
        <div class="col-lg-4">
          <div class="cardx h-100" style="{{ $isFree || $isTestPlan ? 'opacity:.78' : '' }}">
            <div class="kicker">{{ $plan->slug }}</div>
            <h3>{{ $plan->name }}</h3>
            <div style="font-size:38px;font-weight:950;letter-spacing:-.05em;margin:12px 0;">
              {{ $price <= 0 ? __('Grátis') : 'R$ '.number_format($price, 2, ',', '.') }}
              @if ($price > 0)<span class="muted" style="font-size:14px;">/{{ __('mês') }}</span>@endif
            </div>
            <p>{{ __(':count eventos por mês', ['count' => number_format((int) $plan->monthly_event_limit, 0, ',', '.')]) }}</p>
            <p>{{ __(':count dias de retenção', ['count' => (int) $plan->event_retention_days]) }}</p>
            @if ($overage > 0)
              <p>{{ __('Excedente: R$ :price por pacote/evento configurado', ['price' => number_format($overage, 2, ',', '.')]) }}</p>
            @endif
            @if ($features)
              <div class="mt-3">
                @foreach ($features as $feature)
                  <span class="pill me-1 mb-1">{{ __($feature) }}</span>
                @endforeach
              </div>
            @endif
            <div class="mt-4">
              <a class="btnx {{ $isFree || $isTestPlan ? '' : 'primary' }} w-100" href="{{ route('register') }}">{{ $isFree ? __('Começar grátis') : ($isTestPlan ? __('Usar para teste') : __('Começar com este plano')) }}</a>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="cardx">
            <h3>{{ __('Planos em configuração') }}</h3>
            <p>{{ __('Os planos comerciais ainda não foram publicados. Para beta privado, crie um workspace e fale com o time do produto.') }}</p>
            <div class="mt-3"><a class="btnx primary" href="{{ route('register') }}">{{ __('Criar workspace beta') }}</a></div>
          </div>
        </div>
      @endforelse
    </div>
  </section>

  <section class="band">
    <div class="kicker">{{ __('Como escolher') }}</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>{{ __('Individual') }}</h3><p>{{ __('Para validar um repositório, testar o fluxo e manter histórico básico de eventos.') }}</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>{{ __('Time') }}</h3><p>{{ __('Para squads que precisam colaborar em webhooks, notas, tarefas e debugging compartilhado.') }}</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>{{ __('Operação') }}</h3><p>{{ __('Para produtos que dependem de eventos GitHub e precisam de retenção, auditoria e suporte.') }}</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">{{ __('Perguntas comerciais') }}</div>
    <h2>{{ __('O limite mensal protege custo e deixa a cobrança previsível.') }}</h2>
    <p class="lead">
      {{ __('O painel acompanha consumo do workspace. Se o volume crescer, o usuário pode migrar para um plano maior ou tratar excedentes conforme a regra comercial vigente.') }}
    </p>
  </section>
</x-layout>
