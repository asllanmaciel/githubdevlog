@php
  $logs = App\Models\AuditLog::with(['user', 'workspace'])->latest('created_at')->limit(100)->get();
  $total = App\Models\AuditLog::count();
  $sensitive = App\Models\AuditLog::whereIn('action', ['workspace.secret.rotated', 'workspace.data.exported', 'workspace.data.purged', 'billing.checkout.started', 'github.installation.linked'])->count();
@endphp

<x-filament-panels::page>
  <style>
    .audit{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 88% 12%,rgba(80,184,255,.15),transparent 34%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,60px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:36px;font-weight:950}.label{color:var(--muted);font-size:13px}.log{display:grid;grid-template-columns:1fr .8fr .9fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px;margin-bottom:10px}.pill{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}code{color:#b7e4ff}@media(max-width:950px){.metrics,.log{grid-template-columns:1fr}}
  </style>

  <div class="audit">
    <section class="hero">
      <div class="kicker">Operacao auditavel</div>
      <h1 class="title">Quem fez o que, quando e em qual workspace.</h1>
      <p class="lead">Acoes sensiveis precisam deixar rastro: rotacao de secret, exportacao, exclusao, checkout, instalacao GitHub, suporte, notas e tarefas.</p>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $total }}</div><div class="label">registros totais</div></div>
      <div class="metric"><div class="value">{{ $sensitive }}</div><div class="label">acoes sensiveis</div></div>
      <div class="metric"><div class="value">{{ $logs->unique('workspace_id')->count() }}</div><div class="label">workspaces recentes</div></div>
    </section>

    <section class="card">
      <div class="kicker">Ultimos eventos</div>
      @forelse ($logs as $log)
        <div class="log">
          <div><strong>{{ $log->action }}</strong><div class="label">{{ $log->subject_type ? class_basename($log->subject_type).' #'.$log->subject_id : 'sem alvo' }}</div></div>
          <div><span class="pill">{{ $log->workspace?->name ?: 'global' }}</span></div>
          <div><span class="pill">{{ $log->user?->email ?: $log->actor_type }}</span></div>
          <div class="label">{{ optional($log->created_at)->format('d/m/Y H:i:s') }}</div>
        </div>
      @empty
        <p class="label">Nenhum registro de auditoria ainda.</p>
      @endforelse
    </section>
  </div>
</x-filament-panels::page>