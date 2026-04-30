<?php

namespace App\Support;

class GoLiveExecutionKit
{
    public static function report(): array
    {
        $groups = collect([
            [
                'title' => 'Domínio e infraestrutura',
                'status' => self::externalStatus(str_starts_with((string) config('app.url'), 'https://') && ! str_contains((string) config('app.url'), 'localhost') && ! str_contains((string) config('app.url'), 'trycloudflare.com')),
                'objective' => 'Publicar o DevLog AI em URL oficial, com HTTPS, APP_URL final e fila pronta para produção.',
                'steps' => [
                    'Apontar domínio oficial para o servidor de produção.',
                    'Emitir e validar certificado TLS.',
                    'Atualizar APP_URL para a URL final HTTPS.',
                    'Configurar QUEUE_CONNECTION fora de sync e worker supervisionado.',
                    'Confirmar que /health, /status, /login, /register e /webhooks/mercado-pago respondem no domínio final.',
                ],
            ],
            [
                'title' => 'GitHub App oficial',
                'status' => self::externalStatus(filled(config('services.github_app.app_id')) && filled(config('services.github_app.setup_url')) && ! str_contains((string) config('services.github_app.setup_url'), 'your-github-app-slug')),
                'objective' => 'Criar o app oficial no GitHub e conectar instalação, callback e webhook ao workspace correto.',
                'steps' => [
                    'Criar GitHub App com nome, descrição, logo e homepage oficiais.',
                    'Configurar callback URL para /github/callback no domínio final.',
                    'Configurar webhook URL para /webhooks/github-app no domínio final.',
                    'Definir permissões mínimas e eventos suportados.',
                    'Preencher GITHUB_APP_ID, CLIENT_ID, CLIENT_SECRET, PRIVATE_KEY, WEBHOOK_SECRET e SETUP_URL.',
                    'Instalar o app em uma conta/repositório de teste e confirmar evento no dashboard.',
                ],
            ],
            [
                'title' => 'Mercado Pago produção',
                'status' => self::externalStatus(config('services.mercado_pago.environment') === 'production'),
                'objective' => 'Virar credenciais de produção e validar cobrança/webhook real de baixo valor.',
                'steps' => [
                    'Conferir credenciais de produção no .env.',
                    'Configurar webhook Mercado Pago para /webhooks/mercado-pago no domínio final.',
                    'Realizar cobrança real de baixo valor ou fluxo controlado.',
                    'Confirmar BillingEvent, assinatura e notificação no workspace.',
                    'Documentar rollback para sandbox se a validação falhar.',
                ],
            ],
            [
                'title' => 'Screenshots finais de submissão',
                'status' => self::externalStatus(file_exists(base_path('docs/github-submission-evidence.md')) && file_exists(base_path('docs/github-developer-program-submission.md'))),
                'objective' => 'Gerar evidências visuais do ambiente live para GitHub Developer Program e apresentação comercial.',
                'steps' => [
                    'Capturar landing pública com proposta e CTA.',
                    'Capturar dashboard com evento real recebido.',
                    'Capturar card com análise AI gerada.',
                    'Capturar Launch Overview, Go-live, Produto AI e Evidências de release no admin.',
                    'Salvar imagens em pasta de submissão e referenciar no dossiê do GitHub Program.',
                ],
            ],
        ]);

        $done = $groups->where('status', 'pronto')->count();
        $total = max($groups->count(), 1);

        return [
            'percent' => (int) round(($done / $total) * 100),
            'done' => $done,
            'total' => $total,
            'groups' => $groups,
            'commit_message' => 'chore: prepara kit operacional de go-live',
        ];
    }

    private static function externalStatus(bool $done): string
    {
        return $done ? 'pronto' : 'externo';
    }
}
