@php
  $users = \App\Models\User::latest()->limit(20)->get();
  $workspaces = \App\Models\Workspace::latest()->limit(20)->get();
  $eventsCount = \App\Models\WebhookEvent::count();
  $tickets = \App\Models\SupportTicket::latest()->limit(20)->get();
  $roadmap = \App\Models\RoadmapItem::orderBy('position')->orderBy('id')->get();
  $plans = \App\Models\BillingPlan::orderBy('price_cents')->get();
  $doneCount = $roadmap->where('status', 'done')->count();
  $totalCount = max($roadmap->count(), 1);
  $generalPercent = round(($doneCount / $totalCount) * 100);
  $pendingCount = $roadmap->where('status', '!=', 'done')->count();
@endphp

<x-filament-panels::page>
  <style>
    .devlog-admin-skin {--bg:#090d12;--panel:#101720;--panel-2:#131d28;--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .devlog-admin-skin *{box-sizing:border-box}.devlog-kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.devlog-hero{display:grid;grid-template-columns:1.15fr .85fr;gap:16px;margin-bottom:16px}.devlog-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.86);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18);overflow:hidden;position:relative}.devlog-card:before{content:"";position:absolute;inset:auto -70px -80px auto;width:180px;height:180px;border-radius:50%;background:rgba(80,184,255,.1);pointer-events:none}.devlog-card>*{position:relative}.devlog-title{font-size:clamp(32px,4.6vw,62px);line-height:.96;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.devlog-lead{color:var(--muted);font-size:17px;line-height:1.65;margin:14px 0 0;max-width:820px}.devlog-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}.devlog-btn{border:1px solid var(--line);border-radius:10px;background:rgba(16,23,32,.92);color:var(--ink);padding:10px 13px;font-weight:850;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px}.devlog-btn.primary{background:var(--blue);border-color:var(--blue);color:#071018}.devlog-btn.success{background:var(--green);border-color:var(--green);color:#071018}.devlog-orb{width:118px;height:118px;border-radius:34px;display:grid;place-items:center;background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.13) 42%,rgba(8,16,25,.94) 72%);border:1px solid rgba(105,227,154,.3);box-shadow:0 22px 60px rgba(105,227,154,.12),inset 0 0 0 8px rgba(105,227,154,.04);font-size:30px;font-weight:950}.devlog-metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.devlog-metric{border:1px solid var(--line);border-radius:16px;background:linear-gradient(180deg,rgba(16,23,32,.92),rgba(15,23,31,.78));padding:16px;min-height:112px;position:relative;overflow:hidden}.devlog-metric:after{content:"";position:absolute;right:-30px;top:-30px;width:90px;height:90px;border-radius:50%;background:rgba(80,184,255,.1)}.devlog-value{font-size:34px;font-weight:950;letter-spacing:-.05em;color:var(--ink);position:relative}.devlog-label{color:var(--muted);font-size:13px;position:relative}.devlog-layout{display:grid;grid-template-columns:330px 1fr;gap:16px;align-items:start}.devlog-side{display:grid;gap:12px;position:sticky;top:92px}.devlog-list{display:grid;gap:10px}.devlog-list-row{border:1px solid var(--line);border-radius:12px;padding:11px;background:#0b1118;color:var(--muted);font-size:13px;line-height:1.45}.devlog-list-row strong{color:var(--ink)}.devlog-board{display:grid;gap:16px}.devlog-phase{border:1px solid var(--line);border-radius:18px;background:linear-gradient(180deg,rgba(16,23,32,.94),rgba(10,15,21,.91));padding:18px;box-shadow:0 18px 50px rgba(0,0,0,.16);position:relative;overflow:hidden}.devlog-phase:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 92% 0%,rgba(80,184,255,.14),transparent 34%),radial-gradient(circle at 10% 100%,rgba(105,227,154,.1),transparent 30%);pointer-events:none}.devlog-phase>*{position:relative}.devlog-phase-head{display:flex;justify-content:space-between;gap:16px;align-items:flex-start;flex-wrap:wrap;margin-bottom:14px}.devlog-phase-title{font-size:22px;font-weight:950;letter-spacing:-.03em;margin:0;color:var(--ink)}.devlog-phase-count{color:var(--muted);font-size:13px;margin-top:4px}.devlog-percent{min-width:82px;height:82px;border-radius:24px;display:grid;place-items:center;border:1px solid rgba(80,184,255,.28);background:rgba(80,184,255,.08);color:var(--ink);font-size:24px;font-weight:950;box-shadow:inset 0 0 0 6px rgba(80,184,255,.06)}.devlog-progress{height:10px;border-radius:999px;background:#0b1118;border:1px solid var(--line);overflow:hidden;margin-bottom:14px}.devlog-progress span{display:block;height:100%;border-radius:999px;background:linear-gradient(90deg,var(--blue),var(--green));box-shadow:0 0 20px rgba(80,184,255,.28)}.devlog-items{display:grid;gap:10px}.devlog-item{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid rgba(39,53,68,.92);border-radius:14px;padding:12px;background:rgba(11,17,24,.8)}.devlog-item.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.devlog-check{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);color:var(--muted);font-weight:950;background:#081019}.devlog-item.done .devlog-check{background:var(--green);border-color:var(--green);color:#071018}.devlog-item-title{font-weight:950;letter-spacing:-.01em;color:var(--ink)}.devlog-item-desc{color:var(--muted);line-height:1.55;margin-top:3px}.devlog-meta{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px}.devlog-pill{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;background:rgba(8,16,25,.74)}
    @media(max-width:1100px){.devlog-hero,.devlog-layout{grid-template-columns:1fr}.devlog-side{position:static}}@media(max-width:760px){.devlog-metrics{grid-template-columns:repeat(2,1fr)}.devlog-item{grid-template-columns:auto 1fr}.devlog-item-action{grid-column:1/-1}.devlog-item-action .devlog-btn{width:100%}}
  </style>

  <div class="devlog-admin-skin">
    <section class="devlog-hero">
      <div class="devlog-card">
        <div class="devlog-kicker">Admin / Produto</div>
        <h1 class="devlog-title">Roadmap visual de lançamento.</h1>
        <p class="devlog-lead">Cockpit do produto para acompanhar o que já foi entregue, o que ainda depende de go-live externo e quais frentes sustentam a candidatura no GitHub Developer Program.</p>
        <div class="devlog-actions">
          <a class="devlog-btn primary" href="{{ url('/admin/docs') }}">Docs admin</a>
          <a class="devlog-btn" href="{{ url('/admin/launch-overview') }}">Launch overview</a>
          <a class="devlog-btn" href="{{ url('/admin/go-live') }}">Go-live</a>
        </div>
      </div>
      <div class="devlog-card" style="display:flex; align-items:center; justify-content:space-between; gap:18px">
        <div>
          <div class="devlog-kicker">Progresso geral</div>
          <div class="devlog-value">{{ $generalPercent }}%</div>
          <div class="devlog-label">{{ $doneCount }} de {{ $roadmap->count() }} item(ns) concluído(s)</div>
          <div class="devlog-label">{{ $pendingCount }} pendência(s), todas ligadas ao go-live externo</div>
          <div class="devlog-progress" style="margin-top:18px"><span style="width: {{ $generalPercent }}%"></span></div>
        </div>
        <div class="devlog-orb">{{ $generalPercent }}%</div>
      </div>
    </section>

    <section class="devlog-metrics">
      <div class="devlog-metric"><div class="devlog-value">{{ $eventsCount }}</div><div class="devlog-label">webhooks no ambiente</div></div>
      <div class="devlog-metric"><div class="devlog-value">{{ $users->count() }}</div><div class="devlog-label">usuários recentes</div></div>
      <div class="devlog-metric"><div class="devlog-value">{{ $workspaces->count() }}</div><div class="devlog-label">workspaces recentes</div></div>
      <div class="devlog-metric"><div class="devlog-value">{{ $tickets->where('status', 'open')->count() }}</div><div class="devlog-label">chamados abertos</div></div>
    </section>

    <div class="devlog-layout">
      <aside class="devlog-side">
        <div class="devlog-card">
          <div class="devlog-kicker">Planos</div>
          <div class="devlog-list">
            @foreach ($plans as $plan)
              <div class="devlog-list-row"><strong>{{ $plan->name }}</strong><br>R$ {{ number_format($plan->price_cents / 100, 2, ',', '.') }} · {{ $plan->monthly_event_limit }} eventos/mês</div>
            @endforeach
          </div>
        </div>
        <div class="devlog-card">
          <div class="devlog-kicker">Suporte recente</div>
          <div class="devlog-list">
            @forelse ($tickets as $ticket)
              <div class="devlog-list-row"><strong>{{ $ticket->subject }}</strong><br>{{ $ticket->status }} · {{ $ticket->priority }}</div>
            @empty
              <div class="devlog-list-row">Nenhum chamado ainda.</div>
            @endforelse
          </div>
        </div>
      </aside>

      <main class="devlog-board">
        @foreach ($roadmap->groupBy('area') as $area => $items)
          @php
            $done = $items->where('status', 'done')->count();
            $total = max($items->count(), 1);
            $percent = round(($done / $total) * 100);
          @endphp
          <article class="devlog-phase">
            <div class="devlog-phase-head">
              <div><h2 class="devlog-phase-title">{{ $area }}</h2><div class="devlog-phase-count">{{ $done }} de {{ $items->count() }} item(ns) concluído(s)</div></div>
              <div class="devlog-percent">{{ $percent }}%</div>
            </div>
            <div class="devlog-progress"><span style="width: {{ $percent }}%"></span></div>
            <div class="devlog-items">
              @foreach ($items as $item)
                <form method="POST" action="{{ route('admin.roadmap.toggle', $item) }}" class="devlog-item {{ $item->status === 'done' ? 'done' : '' }}">
                  @csrf
                  <div class="devlog-check">{{ $item->status === 'done' ? '✓' : '' }}</div>
                  <div>
                    <div class="devlog-item-title">{{ $item->title }}</div>
                    <div class="devlog-item-desc">{{ $item->description }}</div>
                    <div class="devlog-meta">
                      <span class="devlog-pill">{{ $item->priority }}</span>
                      <span class="devlog-pill">{{ $item->status === 'done' ? 'Concluído' : 'Pendente' }}</span>
                      @if($item->completed_at)<span class="devlog-pill">finalizado em {{ $item->completed_at->format('d/m/Y') }}</span>@endif
                    </div>
                  </div>
                  <div class="devlog-item-action"><button class="devlog-btn {{ $item->status === 'done' ? '' : 'success' }}" type="submit">{{ $item->status === 'done' ? 'Reabrir' : 'Concluir' }}</button></div>
                </form>
              @endforeach
            </div>
          </article>
        @endforeach
      </main>
    </div>
  </div>
</x-filament-panels::page>