<x-layout title="Planos - GitHub DevLog AI">
  <main class="hero">
    <span class="eyebrow">Planos para devs e timês</span>
    <h1>Comece pequeno, escale quando seus webhooks virarem rotina de produto.</h1>
    <p class="lead">
      O DevLog AI foi pensado para cobrar por uso real: eventos recebidos, retenção, colaboracao e operação. Escolha um plano, conecte seu repositório e acompanhe tudo no workspace.
    </p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="{{ route('register') }}">Criar workspace</a>
      <a class="btnx" href="{{ route('docs.users') }}">Ver guia de uso</a>
    </div>
  </main>

  <section class="band">
    <div class="kicker">Modelo comercial</div>
    <h2>Preço conectado ao volume de eventos, não a promêssas vagas.</h2>
    <p class="lead">
      Cada plano define limite mensal, retenção e recursos. Quando houver excedente, o painel deve avisar antes do bloqueio ou da cobranca adicional.
    </p>

    <div class="row g-3 mt-3">
      @forelse ($plans as $plan)
        @php
          $price = ((int) $plan->price_cents) / 100;
          $overage = ((int) ($plan->overage_price_cents ?? 0)) / 100;
          $features = is_array($plan->features) ? $plan->features : [];
        @endphp
        <div class="col-lg-4">
          <div class="cardx h-100">
            <div class="kicker">{{ $plan->slug }}</div>
            <h3>{{ $plan->name }}</h3>
            <div style="font-size:38px;font-weight:950;letter-spacing:-.05em;margin:12px 0;">
              {{ $price <= 0 ? 'Grátis' : 'R$ '.number_format($price, 2, ',', '.') }}
              @if ($price > 0)<span class="muted" style="font-size:14px;">/mês</span>@endif
            </div>
            <p>{{ number_format((int) $plan->monthly_event_limit, 0, ',', '.') }} eventos por mês</p>
            <p>{{ (int) $plan->event_retention_days }} dias de retenção</p>
            @if ($overage > 0)
              <p>Excedente: R$ {{ number_format($overage, 2, ',', '.') }} por pacote/evento configurado</p>
            @endif
            @if ($features)
              <div class="mt-3">
                @foreach ($features as $feature)
                  <span class="pill me-1 mb-1">{{ $feature }}</span>
                @endforeach
              </div>
            @endif
            <div class="mt-4">
              <a class="btnx primary w-100" href="{{ route('register') }}">Comecar com este perfil</a>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="cardx">
            <h3>Planos em configuração</h3>
            <p>Os planos comerciais ainda não foram públicados. Para beta privado, crie um workspace e fale com o time do produto.</p>
            <div class="mt-3"><a class="btnx primary" href="{{ route('register') }}">Criar workspace beta</a></div>
          </div>
        </div>
      @endforelse
    </div>
  </section>

  <section class="band">
    <div class="kicker">Como escolher</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Individual</h3><p>Para validar um repositório, testar o fluxo e manter histórico basico de eventos.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Time</h3><p>Para squads que precisam colaborar em webhooks, notas, tarefas e debugging compartilhado.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Operacao</h3><p>Para produtos que dependem de eventos GitHub e precisam de retenção, auditoria e suporte.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Perguntas comerciais</div>
    <h2>O limite mensal protege custo e deixa a cobranca previsivel.</h2>
    <p class="lead">
      O painel acompanha consumo do workspace. Se o volume crescer, o usuário pode migrar para um plano maior ou tratar excedentes conforme a regra comercial vigente.
    </p>
  </section>
</x-layout>

