<x-filament-panels::page>
    @php
        $report = App\Support\AiProductReadiness::report();
        $usage = $report['usage'];
        $riskDistribution = $usage['risk_distribution'] ?? [];
        $estimatedCostCents = (int) ($usage['estimated_cost_cents'] ?? 0);
        $estimatedCost = 'R$ ' . number_format($estimatedCostCents / 100, 2, ',', '.');
        $coverage = (int) ($usage['coverage'] ?? 0);
    @endphp

    <style>
        .ai-product-shell {
            display: grid;
            gap: 18px;
        }

        .ai-product-hero {
            border: 1px solid rgba(80, 184, 255, .22);
            border-radius: 28px;
            padding: 28px;
            background:
                radial-gradient(circle at 10% 10%, rgba(80, 184, 255, .16), transparent 28%),
                radial-gradient(circle at 90% 0%, rgba(105, 227, 154, .13), transparent 28%),
                linear-gradient(135deg, rgba(7, 16, 24, .96), rgba(10, 24, 33, .9));
            color: #f3f7fb;
            box-shadow: 0 28px 80px rgba(0, 0, 0, .28);
        }

        .ai-product-kicker {
            color: #69e39a;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .14em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .ai-product-title {
            max-width: 980px;
            font-size: clamp(28px, 4vw, 54px);
            line-height: 1;
            letter-spacing: -.05em;
            font-weight: 950;
            margin: 0 0 12px;
        }

        .ai-product-lead {
            max-width: 900px;
            color: #a9bac8;
            font-size: 16px;
            line-height: 1.75;
            margin: 0;
        }

        .ai-product-metrics {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 12px;
            margin-top: 22px;
        }

        .ai-product-card {
            border: 1px solid rgba(148, 163, 184, .22);
            border-radius: 22px;
            padding: 18px;
            background: rgba(8, 15, 22, .7);
            color: #f3f7fb;
        }

        .ai-product-card strong {
            display: block;
            font-size: 24px;
            line-height: 1;
            letter-spacing: -.04em;
            margin-bottom: 8px;
        }

        .ai-product-card span,
        .ai-product-card p {
            color: #9aa9b5;
            line-height: 1.6;
        }

        .ai-product-grid {
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            gap: 16px;
        }

        .ai-product-checks {
            display: grid;
            gap: 10px;
        }

        .ai-product-check {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 12px;
            align-items: start;
            border: 1px solid rgba(148, 163, 184, .18);
            border-radius: 18px;
            padding: 14px;
            background: rgba(11, 20, 29, .72);
        }

        .ai-product-dot {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: rgba(105, 227, 154, .14);
            color: #69e39a;
            font-weight: 950;
        }

        .ai-product-dot.pending {
            background: rgba(255, 209, 102, .13);
            color: #ffd166;
        }

        .ai-product-progress {
            height: 12px;
            border-radius: 999px;
            overflow: hidden;
            background: rgba(148, 163, 184, .14);
            margin-top: 14px;
        }

        .ai-product-progress span {
            display: block;
            width: {{ $coverage }}%;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #50b8ff, #69e39a);
        }

        .ai-product-list {
            display: grid;
            gap: 10px;
            margin-top: 12px;
        }

        .ai-product-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            border-bottom: 1px solid rgba(148, 163, 184, .12);
            padding-bottom: 10px;
            color: #c8d5df;
        }

        .ai-product-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        @media (max-width: 1100px) {
            .ai-product-metrics { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .ai-product-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 720px) {
            .ai-product-metrics { grid-template-columns: 1fr; }
            .ai-product-hero { padding: 22px; }
        }
    </style>

    <div class="ai-product-shell">
        <section class="ai-product-hero">
            <div class="ai-product-kicker">DevLog AI · prontidão de produto</div>
            <h1 class="ai-product-title">A camada AI já tem valor técnico, limite comercial e caminho claro de monetização.</h1>
            <p class="ai-product-lead">
                Este painel acompanha se a inteligência do produto está pronta para uso real: análise local gratuita para todos,
                análise avançada paga por uso, auditoria, documentação, cobertura de eventos e custo estimado para proteger margem.
            </p>

            <div class="ai-product-metrics">
                <div class="ai-product-card">
                    <strong>{{ $usage['total_events'] }}</strong>
                    <span>eventos no ambiente</span>
                </div>
                <div class="ai-product-card">
                    <strong>{{ $usage['analyzed_events'] }}</strong>
                    <span>eventos com análise AI</span>
                </div>
                <div class="ai-product-card">
                    <strong>{{ $usage['advanced_events'] ?? 0 }}</strong>
                    <span>análises avançadas</span>
                </div>
                <div class="ai-product-card">
                    <strong>{{ $estimatedCost }}</strong>
                    <span>custo estimado de AI</span>
                </div>
                <div class="ai-product-card">
                    <strong>{{ $coverage }}%</strong>
                    <span>cobertura analisada</span>
                </div>
                <div class="ai-product-card">
                    <strong>{{ $report['ready'] ? 'sim' : 'não' }}</strong>
                    <span>camada pronta</span>
                </div>
            </div>

            <div class="ai-product-progress" aria-label="Cobertura de eventos analisados">
                <span></span>
            </div>
        </section>

        <section class="ai-product-grid">
            <div class="ai-product-card">
                <div class="ai-product-kicker">Checklist técnico</div>
                <div class="ai-product-checks">
                    @foreach ($report['checks'] as $check)
                        <div class="ai-product-check">
                            <div class="ai-product-dot {{ $check['done'] ? '' : 'pending' }}">{{ $check['done'] ? '✓' : '!' }}</div>
                            <div>
                                <strong style="font-size:16px;margin-bottom:4px;">{{ $check['title'] }}</strong>
                                <p style="margin:0;">{{ $check['detail'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="ai-product-card">
                <div class="ai-product-kicker">Leitura comercial</div>
                <strong style="font-size:28px;line-height:1.05;">AI grátis para ativação. AI avançada para receita.</strong>
                <p>
                    A V1 local reduz fricção e entrega valor no plano gratuito. A V2 com LLM entra como recurso premium,
                    controlado por limite mensal e custo estimado por análise. Isso protege margem e justifica upgrades por volume.
                </p>
                <div class="ai-product-list">
                    <div class="ai-product-row"><span>Modelo base</span><b>local-devlog-ai-v1</b></div>
                    <div class="ai-product-row"><span>Modelo avançado</span><b>OpenAI configurável</b></div>
                    <div class="ai-product-row"><span>Cobrança recomendada</span><b>pacote por plano + excedente</b></div>
                    <div class="ai-product-row"><span>Proteção operacional</span><b>limite, auditoria e custo salvo</b></div>
                </div>
            </div>
        </section>

        <section class="ai-product-grid">
            <div class="ai-product-card">
                <div class="ai-product-kicker">Distribuição de risco</div>
                <div class="ai-product-list">
                    @forelse ($riskDistribution as $risk => $count)
                        <div class="ai-product-row">
                            <span>{{ ucfirst($risk ?: 'sem risco') }}</span>
                            <b>{{ $count }}</b>
                        </div>
                    @empty
                        <p>Nenhuma análise de risco gerada ainda.</p>
                    @endforelse
                </div>
            </div>

            <div class="ai-product-card">
                <div class="ai-product-kicker">Próximas evoluções</div>
                <div class="ai-product-list">
                    @foreach ($report['next_steps'] as $step)
                        <div class="ai-product-row">
                            <span>{{ $step }}</span>
                            <b>planejado</b>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
