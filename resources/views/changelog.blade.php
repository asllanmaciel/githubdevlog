<x-layout title="Changelog - GitHub DevLog AI">
  <main class="hero">
    <span class="eyebrow">Produto em evolução</span>
    <h1>Changelog público do GitHub DevLog AI.</h1>
    <p class="lead">
      Acompanhe as entregas que aproximam o produto do live: infraestrutura SaaS, GitHub App, billing, segurança, suporte, docs e experiencia de uso para devs.
    </p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="{{ route('register') }}">Criar workspace</a>
      <a class="btnx" href="{{ route('pricing') }}">Ver planos</a>
    </div>
  </main>

  <section class="band">
    <div class="kicker">Ultimas entregas</div>
    <h2>Transparencia para quem vai confiar webhooks ao produto.</h2>
    <p class="lead">O changelog usa as entregas concluidas do roadmap para mostrar progresso público sem expor detalhes sensíveis de administracao.</p>

    <div class="row g-3 mt-3">
      @forelse ($entries as $entry)
        <div class="col-lg-6">
          <article class="cardx h-100">
            <div class="d-flex justify-content-between gap-2 flex-wrap mb-2">
              <span class="pill">{{ $entry['area'] }}</span>
              <span class="muted">{{ $entry['date'] }}</span>
            </div>
            <h3>{{ $entry['title'] }}</h3>
            <p>{{ $entry['description'] ?: 'Entrega concluida no ciclo de evolução do produto.' }}</p>
          </article>
        </div>
      @empty
        <div class="col-12">
          <div class="cardx">
            <h3>Primeiras entregas em preparacao</h3>
            <p>O changelog sera públicado assim que as primeiras entregas forem marcadas como concluidas no roadmap.</p>
          </div>
        </div>
      @endforelse
    </div>
  </section>

  <section class="band">
    <div class="kicker">Por que isso importa</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>ConfianÃ§a</h3><p>Devs conseguem ver que o produto tem manutenção ativa e direcao clara.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Produto</h3><p>Cada entrega mostra progresso concreto rumo ao uso real, não apenas promêssa de roadmap.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Launch</h3><p>Facilita comunicacao durante beta, demos, submissao ao GitHub e abertura pública.</p></div></div>
    </div>
  </section>
</x-layout>

