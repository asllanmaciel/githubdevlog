@php
  $workspaces = App\Models\Workspace::with(['subscription.plan'])->withCount('webhookEvents')->orderBy('name')->get();
@endphp

<x-filament-panels::page>
  <style>
    .gov{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 88% 10%,rgba(105,227,154,.14),transparent 34%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,60px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.grid{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start}.row{display:grid;grid-template-columns:1.2fr .8fr .5fr .9fr;gap:10px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px;margin-bottom:10px}.label{color:var(--muted);font-size:13px}.pill{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}@media(max-width:1000px){.grid,.row{grid-template-columns:1fr}}
  </style>

  <div class="gov">
    <section class="hero">
      <div class="kicker">LGPD, suporte e portabilidade</div>
      <h1 class="title">Dados do workspace exportaveis e auditaveis.</h1>
      <p class="lead">Um SaaS que recebe webhooks precisa conseguir responder com clareza: quais dados temos, de qual workspace, por quanto tempo e como entregar uma copia quando necessario.</p>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Workspaces</div>
        @forelse ($workspaces as $workspace)
          <div class="row">
            <div><strong>{{ $workspace->name }}</strong><div class="label">{{ $workspace->uuid }}</div></div>
            <div><span class="pill">{{ $workspace->subscription?->plan?->name ?: 'Sem plano' }}</span></div>
            <div><strong>{{ $workspace->webhook_events_count }}</strong><div class="label">eventos</div></div>
            <div><code>php artisan devlog:export-workspace-data {{ $workspace->id }}</code></div>
          </div>
        @empty
          <p class="label">Nenhum workspace encontrado.</p>
        @endforelse
      </div>

      <aside class="card">
        <div class="kicker">Comandos</div>
        <pre class="cmd">php artisan devlog:export-workspace-data {id}
php artisan devlog:export-workspace-data {uuid} --json
php artisan devlog:export-workspace-data {slug} --output=exports/workspace.json</pre>
        <div class="label" style="line-height:1.65">O arquivo e salvo em storage/app/exports e inclui workspace, membros, plano, repositorios, instalacoes GitHub, eventos sanitizados, notas, tarefas, snapshots e faturas internas.</div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>