<x-filament-panels::page>
  <style>
    .bugs{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--red:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.9);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 88% 10%,rgba(255,107,107,.16),transparent 34%),rgba(16,23,32,.9)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(34px,5vw,60px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}
    .lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:920px}
    .metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}
    .metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}
    .value{font-size:36px;font-weight:950}.label,.muted{color:var(--muted);font-size:13px;line-height:1.55}
    .grid{display:grid;grid-template-columns:1fr 390px;gap:16px;align-items:start}
    .bug{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px;margin-bottom:10px}
    .bug.open{border-color:rgba(255,107,107,.42);background:rgba(255,107,107,.06)}.bug.resolved{border-color:rgba(105,227,154,.34);background:rgba(105,227,154,.06)}
    .row{display:flex;gap:8px;align-items:center;justify-content:space-between;flex-wrap:wrap}.pills{display:flex;gap:6px;flex-wrap:wrap;margin-top:10px}
    .pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.pill.danger{color:#071018;background:var(--red);border-color:var(--red);font-weight:950}.pill.ok{color:#071018;background:var(--green);border-color:var(--green);font-weight:950}
    .msg{margin-top:8px;color:var(--ink);font-size:13px;line-height:1.55;word-break:break-word}.code{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;color:#b7e4ff;font-size:12px;word-break:break-word}
    .btn{border:0;border-radius:10px;background:linear-gradient(135deg,var(--blue),var(--green));color:#061018;font-weight:950;padding:8px 11px}.btn.warn{background:var(--yellow)}
    .empty{border:1px dashed var(--line);border-radius:16px;padding:18px;color:var(--muted)}
    @media(max-width:1100px){.grid,.metrics{grid-template-columns:1fr}}
  </style>

  <div class="bugs">
    <section class="hero">
      <div class="kicker">Operacao / Observabilidade</div>
      <h1 class="title">Monitor interno de bugs.</h1>
      <p class="lead">Excecoes 500 e falhas inesperadas sao agrupadas por fingerprint, contadas e exibidas aqui para triagem rapida sem depender de servico externo.</p>
    </section>

    @if (! $report['available'])
      <div class="empty">Tabela <code>bug_reports</code> ausente. Rode <code>php artisan migrate --force</code>.</div>
    @else
      <section class="metrics">
        <div class="metric"><div class="value">{{ $report['open_count'] }}</div><div class="label">bugs abertos</div></div>
        <div class="metric"><div class="value">{{ $report['today_count'] }}</div><div class="label">vistos hoje</div></div>
        <div class="metric"><div class="value">{{ $report['resolved_count'] }}</div><div class="label">resolvidos</div></div>
      </section>

      <section class="grid">
        <div class="card">
          <div class="kicker">Ultimas ocorrencias</div>
          @forelse ($report['latest'] as $bug)
            <article class="bug {{ $bug->resolved_at ? 'resolved' : 'open' }}">
              <div class="row">
                <strong>#{{ $bug->id }} {{ class_basename($bug->exception_class) }}</strong>
                <button class="btn {{ $bug->resolved_at ? 'warn' : '' }}" type="button" wire:click="{{ $bug->resolved_at ? 'reopenBug' : 'resolveBug' }}({{ $bug->id }})">
                  {{ $bug->resolved_at ? 'Reabrir' : 'Resolver' }}
                </button>
              </div>
              <div class="msg">{{ $bug->message }}</div>
              <div class="pills">
                <span class="pill {{ $bug->resolved_at ? 'ok' : 'danger' }}">{{ $bug->resolved_at ? 'resolvido' : $bug->level }}</span>
                <span class="pill">x{{ $bug->occurrences }}</span>
                <span class="pill">{{ $bug->last_seen_at?->format('d/m H:i') }}</span>
                @if($bug->route)<span class="pill">{{ $bug->route }}</span>@endif
              </div>
              <div class="code" style="margin-top:10px">{{ $bug->file }}:{{ $bug->line }}</div>
            </article>
          @empty
            <div class="empty">Nenhum bug capturado ainda.</div>
          @endforelse
        </div>

        <aside class="card">
          <div class="kicker">Mais recorrentes abertos</div>
          @forelse ($report['top'] as $bug)
            <div class="bug open">
              <div class="row"><strong>#{{ $bug->id }}</strong><span class="pill danger">x{{ $bug->occurrences }}</span></div>
              <div class="msg">{{ class_basename($bug->exception_class) }}</div>
              <div class="muted">{{ $bug->last_seen_at?->diffForHumans() }}</div>
            </div>
          @empty
            <p class="muted">Nada aberto por enquanto.</p>
          @endforelse

          <div class="kicker" style="margin-top:18px">CLI</div>
          <div class="code">php artisan devlog:bug-monitor<br>php artisan devlog:bug-monitor --json</div>
        </aside>
      </section>
    @endif
  </div>
</x-filament-panels::page>
