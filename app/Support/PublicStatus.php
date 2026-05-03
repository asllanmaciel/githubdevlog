<?php

namespace App\Support;

class PublicStatus
{
    public static function report(): array
    {
        $health = SystemHealth::report();
        $incident = IncidentResponse::report();

        $components = collect([
            ['name' => 'Aplicacao web', 'status' => $health['ok'] ? 'operational' : 'degraded', 'detail' => 'Landing, login, dashboard e painel admin.'],
            ['name' => 'Banco de dados', 'status' => $health['checks']['database']['ok'] ? 'operational' : 'outage', 'detail' => 'Persistencia de workspaces, eventos e billing.'],
            ['name' => 'Webhooks GitHub', 'status' => $health['checks']['github_webhooks']['ok'] ? 'operational' : 'degraded', 'detail' => 'Recebimento e validacao de eventos.'],
            ['name' => 'Mercado Pago', 'status' => $health['checks']['billing']['ok'] ? 'operational' : 'degraded', 'detail' => 'Checkout, webhooks e faturas de uso.'],
            ['name' => 'Filas e jobs', 'status' => $incident['metrics']['failed_jobs'] === 0 ? 'operational' : 'degraded', 'detail' => 'Rotinas agendadas e processamento assincrono.'],
            ['name' => 'Suporte', 'status' => $incident['metrics']['open_tickets'] < 10 ? 'operational' : 'degraded', 'detail' => 'Atendimento e base de conhecimento.'],
        ]);

        $overall = $components->contains('status', 'outage')
            ? 'outage'
            : ($components->contains('status', 'degraded') ? 'degraded' : 'operational');

        return [
            'overall' => $overall,
            'components' => $components,
            'updated_at' => now(),
        ];
    }
}
