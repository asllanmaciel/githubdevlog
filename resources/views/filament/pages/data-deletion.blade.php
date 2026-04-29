@php
  $workspaces = App\Models\Workspace::withCount(['members', 'webhookEvents', 'repositories'])->orderBy('name')->get();
@endphp

<x-filament-panels::page>
  <style>
    .delete{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--danger:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 88% 12%,rgba(255,107,107,.14),transparent 34%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,60px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.grid{display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start}.row{display:grid;grid-template-columns:1.2fr .4fr .4fr .4fr 1fr;gap:10px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px;margin-bottom:10px}.label{color:var(--muted);font-size:13px}.pill{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}.danger{color:var(--danger)}@media(max-width:1050px){.grid,.row{grid-template-columns:1fr}}
  </style>

  <div class="delete">
    <section class="hero">
      <div class="kicker">Compliance e suporte</div>
      <h1 class="title">Exclusao controlada, nunca acidental.</h1>
      <p class="lead">Pedidos de exclusao precisam ser previsiveis: simular impacto, exportar antes se necessario e so apagar com confirmacao explicita via terminal.</p>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Workspaces</div>
        @forelse ($workspaces as $workspace)
          <div class="row">
            <div><strong>{{ $workspace->name }}</strong><div class="label">{{ $workspace->uuid }}</div></div>
            <div><strong>{{ $workspace->members_count }}</strong><div class="label">membros</div></div>
            <div><strong>{{ $workspace->repositories_count }}</strong><div class="label">repos</div></div>
            <div><strong>{{ $workspace->webhook_events_count }}</strong><div class="label">eventos</div></div>
            <div><code>php artisan devlog:purge-workspace-data {{ $workspace->id }} --dry-run</code></div>
          </div>
        @empty
          <p class="label">Nenhum workspace encontrado.</p>
        @endforelse
      </div>

      <aside class="card">
        <div class="kicker">Fluxo seguro</div>
        <pre class="cmd">php artisan devlog:export-workspace-data {id}
php artisan devlog:purge-workspace-data {id} --dry-run
php artisan devlog:purge-workspace-data {id} --force</pre>
        <p class="label" style="line-height:1.65">Por padrao, o comando sempre simula. A exclusao real exige <strong class="danger">--force</strong>.</p>
        <div class="kicker" style="margin-top:18px">Remove</div>
        <span class="pill">eventos</span> <span class="pill">notas</span> <span class="pill">tarefas</span> <span class="pill">membros</span> <span class="pill">repositorios</span> <span class="pill">assinatura</span> <span class="pill">instalacoes GitHub</span>
      </aside>
    </section>
  </div>
</x-filament-panels::page>