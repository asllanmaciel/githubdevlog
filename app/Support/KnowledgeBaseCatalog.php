<?php

namespace App\Support;

use App\Models\KnowledgeBaseArticle;

class KnowledgeBaseCatalog
{
    public static function sync(): int
    {
        $synced = 0;

        foreach (self::articles() as $article) {
            $model = KnowledgeBaseArticle::updateOrCreate(
                ['slug' => $article['slug']],
                $article + ['published' => true],
            );

            if ($model->wasRecentlyCreated || $model->wasChanged()) {
                $synced++;
            }
        }

        return $synced;
    }

    public static function expectedTotal(): int
    {
        return count(self::articles());
    }

    public static function articles(): array
    {
        return [
            [
                'title' => 'Como conectar um repositório GitHub',
                'slug' => 'conectar-repositorio-github',
                'category' => 'webhooks',
                'summary' => 'Configure Payload URL, secret e eventos recomendados para começar a receber entregas.',
                'body' => "1. Abra Settings > Webhooks no repositório.\n2. Cole o Payload URL do workspace.\n3. Escolha application/json.\n4. Cole o secret do workspace.\n5. Envie um ping e acompanhe no dashboard.",
                'position' => 10,
            ],
            [
                'title' => 'Por que meu webhook foi rejeitado?',
                'slug' => 'webhook-rejeitado',
                'category' => 'webhooks',
                'summary' => 'Entenda falhas comuns de assinatura, endpoint, content type e limite mensal.',
                'body' => 'Verifique se o secret no GitHub e no workspace são iguais, se o content type é application/json, se a URL pública usa HTTPS e se o workspace ainda tem limite mensal disponível.',
                'position' => 20,
            ],
            [
                'title' => 'Como funciona a segurança dos payloads',
                'slug' => 'seguranca-dos-payloads',
                'category' => 'security',
                'summary' => 'Resumo de validação HMAC, isolamento por workspace, sanitização e retenção.',
                'body' => 'O DevLog valida assinaturas GitHub com HMAC SHA-256, isola eventos por workspace, sanitiza payloads sensíveis antes de armazenar e aplica retenção conforme o plano ativo.',
                'position' => 30,
            ],
            [
                'title' => 'Planos, limites e uso mensal',
                'slug' => 'planos-limites-uso-mensal',
                'category' => 'billing',
                'summary' => 'Como acompanhar eventos usados, limite mensal, AI avançada e retenção.',
                'body' => 'Cada plano define limite mensal de eventos, retenção e cota de análises AI avançadas. O dashboard mostra consumo, eventos restantes e sinais de upgrade antes de bloquear novos webhooks.',
                'position' => 40,
            ],
            [
                'title' => 'Checklist do GitHub App oficial',
                'slug' => 'checklist-github-app-oficial',
                'category' => 'github-app',
                'summary' => 'URLs, permissões e eventos recomendados para publicar a integração oficial.',
                'body' => 'Use callback /github/callback, webhook /webhooks/github-app, permissões read-only e eventos push, pull_request, workflow_run, issues, installation e installation_repositories.',
                'position' => 50,
            ],
        ];
    }
}
