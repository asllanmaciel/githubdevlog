<?php

namespace App\Console\Commands;

use App\Models\BillingPlan;
use App\Models\KnowledgeBaseArticle;
use App\Models\Notification;
use App\Models\Repository;
use App\Models\RoadmapItem;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Models\WebhookEventNote;
use App\Models\WebhookEventTask;
use App\Models\Workspace;
use App\Models\WorkspaceSubscription;
use App\Support\KnowledgeBaseCatalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DevlogSeedDemo extends Command
{
    protected $signature = 'devlog:seed-demo
        {--fresh : Remove apenas os dados demo antes de recriar}
        {--email=demo@devlog.local : Email do usuario demo}
        {--password=DevlogDemo123! : Senha do usuario demo}';

    protected $description = 'Cria um cenario demo completo para apresentacao, QA e submissao.';

    public function handle(): int
    {
        $email = (string) $this->option('email');
        $password = (string) $this->option('password');

        if ($this->option('fresh')) {
            $this->cleanDemoData($email);
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Dev Demo',
                'password' => Hash::make($password),
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $plan = BillingPlan::updateOrCreate(
            ['slug' => 'growth-demo'],
            [
                'name' => 'Growth Demo',
                'price_cents' => 4900,
                'currency' => 'BRL',
                'event_retention_days' => 90,
                'monthly_event_limit' => 25000,
                'monthly_ai_analysis_limit' => 100,
                'ai_analysis_overage_price_cents' => 12,
                'overage_price_cents' => 2,
                'features' => [
                    'Workspace privado',
                    'Historico de webhooks GitHub',
                    'Validacao de assinatura',
                    '100 analises AI avancadas por mes',
                    'Notas e tarefas por evento',
                    'Suporte prioritario',
                ],
                'active' => true,
            ]
        );

        $workspace = Workspace::updateOrCreate(
            ['slug' => 'workspace-demo'],
            [
                'uuid' => 'de2353a1-b258-4da9-ab7c-54b255053b9f',
                'name' => 'Workspace Demo',
                'webhook_secret' => 'dlog_demo_secret_123456789',
                'github_app_installation_id' => 'demo-installation-1001',
            ]
        );

        $workspace->users()->syncWithoutDetaching([
            $user->id => ['role' => 'owner'],
        ]);

        WorkspaceSubscription::updateOrCreate(
            ['workspace_id' => $workspace->id],
            [
                'billing_plan_id' => $plan->id,
                'provider' => 'mercado_pago',
                'provider_reference' => 'demo-subscription-growth',
                'status' => 'active',
                'trial_ends_at' => now()->addDays(14),
                'current_period_ends_at' => now()->addMonth(),
            ]
        );

        $repo = Repository::updateOrCreate(
            ['workspace_id' => $workspace->id, 'full_name' => 'demo/api'],
            [
                'github_id' => '100200300',
                'private' => true,
                'default_branch' => 'main',
            ]
        );

        $events = $this->seedWebhookEvents($workspace, $repo);
        $this->seedCollaboration($user, $events);
        $this->seedKnowledgeBase();
        $this->seedSupport($workspace, $user);
        $this->seedNotifications($workspace, $user);
        $this->seedRoadmap();

        $this->info('Demo criada/atualizada com sucesso.');
        $this->line('Login: '.$email);
        $this->line('Senha: '.$password);
        $this->line('Workspace: '.$workspace->name.' ('.$workspace->uuid.')');
        $this->line('Webhook GitHub manual: '.url('/webhooks/github/'.$workspace->uuid));

        return self::SUCCESS;
    }

    private function seedWebhookEvents(Workspace $workspace, Repository $repo): array
    {
        $samples = [
            [
                'event_name' => 'push',
                'action' => null,
                'delivery_id' => 'demo-delivery-push-001',
                'received_at' => now()->subMinutes(8),
                'payload' => [
                    'ref' => 'refs/heads/main',
                    'repository' => ['full_name' => $repo->full_name, 'private' => true],
                    'pusher' => ['name' => 'dev-demo'],
                    'head_commit' => ['message' => 'feat: conecta webhook ao workspace privado'],
                ],
            ],
            [
                'event_name' => 'pull_request',
                'action' => 'opened',
                'delivery_id' => 'demo-delivery-pr-001',
                'received_at' => now()->subMinutes(22),
                'payload' => [
                    'action' => 'opened',
                    'repository' => ['full_name' => $repo->full_name, 'private' => true],
                    'pull_request' => ['number' => 42, 'title' => 'Adicionar tracking de eventos de billing'],
                ],
            ],
            [
                'event_name' => 'workflow_run',
                'action' => 'completed',
                'delivery_id' => 'demo-delivery-ci-001',
                'received_at' => now()->subHour(),
                'payload' => [
                    'action' => 'completed',
                    'repository' => ['full_name' => $repo->full_name, 'private' => true],
                    'workflow_run' => ['name' => 'CI', 'conclusion' => 'success'],
                ],
            ],
        ];

        $events = [];
        foreach ($samples as $sample) {
            $events[] = WebhookEvent::updateOrCreate(
                ['delivery_id' => $sample['delivery_id']],
                [
                    'workspace_id' => $workspace->id,
                    'repository_id' => $repo->id,
                    'source' => 'github',
                    'event_name' => $sample['event_name'],
                    'action' => $sample['action'],
                    'signature_valid' => true,
                    'validation_method' => 'x-hub-signature-256',
                    'headers' => [
                        'x-github-event' => $sample['event_name'],
                        'x-hub-signature-256' => 'sha256=demo',
                    ],
                    'payload' => $sample['payload'],
                    'received_at' => $sample['received_at'],
                    'processed_at' => $sample['received_at']->copy()->addSeconds(2),
                ]
            );
        }

        return $events;
    }

    private function seedCollaboration(User $user, array $events): void
    {
        if (! isset($events[0])) {
            return;
        }

        WebhookEventNote::updateOrCreate(
            ['webhook_event_id' => $events[0]->id, 'user_id' => $user->id],
            ['body' => 'Evento validado. Usar este payload como exemplo na demo do GitHub Developer Program.']
        );

        WebhookEventTask::updateOrCreate(
            ['webhook_event_id' => $events[1]->id ?? $events[0]->id, 'title' => 'Conferir parser do pull_request'],
            [
                'assigned_to' => $user->id,
                'status' => 'open',
                'due_at' => now()->addDays(2),
            ]
        );
    }

    private function seedKnowledgeBase(): void
    {
        KnowledgeBaseCatalog::sync();
    }

    private function seedSupport(Workspace $workspace, User $user): void
    {
        SupportTicket::updateOrCreate(
            ['workspace_id' => $workspace->id, 'subject' => 'Validar primeiro webhook GitHub'],
            [
                'user_id' => $user->id,
                'status' => 'open',
                'priority' => 'normal',
                'message' => 'Ticket demo para mostrar o fluxo de suporte dentro do produto.',
            ]
        );
    }

    private function seedNotifications(Workspace $workspace, User $user): void
    {
        Notification::updateOrCreate(
            ['workspace_id' => $workspace->id, 'user_id' => $user->id, 'title' => 'Workspace pronto para receber webhooks'],
            [
                'body' => 'Configure o Payload URL no GitHub e acompanhe os eventos em tempo real.',
                'type' => 'success',
                'read_at' => null,
            ]
        );
    }

    private function seedRoadmap(): void
    {
        $items = [
            [
                'title' => 'Manifesto de produto e trilha de impacto',
                'area' => 'Estratégia e posicionamento',
                'status' => 'done',
                'priority' => 'alta',
                'description' => 'Definir proposta de valor, ICP, critérios de sucesso e o que diferenciará a experiência do Devlog no mercado em 12 meses.',
                'position' => 10,
                'completed_at' => now(),
            ],
            [
                'title' => 'Arquitetura técnica de referência',
                'area' => 'Fundação do sistema',
                'status' => 'done',
                'priority' => 'alta',
                'description' => 'Padronizar camadas de domínio, serviços, observabilidade e contratos de integração para suportar evolução acelerada sem retrabalho.',
                'position' => 20,
                'completed_at' => now(),
            ],
            [
                'title' => 'Roteiro de dados e privacidade por default',
                'area' => 'Fundação do sistema',
                'status' => 'done',
                'priority' => 'alta',
                'description' => 'Definir retention policy, LGPD-by-design, anonimização e trilha de auditoria para toda alteração de dados sensíveis.',
                'position' => 30,
                'completed_at' => now(),
            ],
            [
                'title' => 'Roadmap visual e governança de prioridades',
                'area' => 'Governança',
                'status' => 'done',
                'priority' => 'media',
                'description' => 'Criar cadência mensal de revisão, critérios de entrada/saída de tarefas e dashboard único de progresso por iniciativa.',
                'position' => 40,
                'completed_at' => now(),
            ],
            [
                'title' => 'Métricas de produto com decisões acionáveis',
                'area' => 'Observabilidade e operação',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Implementar eventos de negócio, funil de conversão e alertas de risco para identificar regressões antes dos usuários perceberem.',
                'position' => 50,
                'completed_at' => null,
            ],
            [
                'title' => 'MVP administrativo com acessibilidade e clareza',
                'area' => 'Produto e UX',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Reforçar linguagem, contraste, estados vazios e mensagens de erro para reduzir atrito e aumentar confiança de uso por admins e times de operação.',
                'position' => 60,
                'completed_at' => null,
            ],
            [
                'title' => 'Fluxo de autenticação robusto e proteção antifraude',
                'area' => 'Segurança',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Finalizar SSO opcional, harden de sessões, proteção contra abuso e trilha de eventos suspeitos com resposta guiada.',
                'position' => 70,
                'completed_at' => null,
            ],
            [
                'title' => 'Hardening de webhooks e tolerância a falhas',
                'area' => 'Confiabilidade',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Adicionar retries com backoff, dead-letter, idempotência e reprocessamento controlado para entregas e eventos perdidos.',
                'position' => 80,
                'completed_at' => null,
            ],
            [
                'title' => 'Catálogo de qualidade de código e revisão contínua',
                'area' => 'Confiabilidade',
                'status' => 'pending',
                'priority' => 'media',
                'description' => 'Implantar linters, regras de complexidade, critérios mínimos de PR e automação de segurança estática no pipeline.',
                'position' => 90,
                'completed_at' => null,
            ],
            [
                'title' => 'Camada de eventos AI de baixa latência',
                'area' => 'IA e inteligência',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Entregar insights de risco e ação em tempo real sem impacto de performance nas rotas críticas do produto.',
                'position' => 100,
                'completed_at' => null,
            ],
            [
                'title' => 'Roteiro de onboarding inteligente',
                'area' => 'Produto e UX',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Oferecer checklist adaptativo por perfil do usuário, com trilha de sucesso para instalação, primeiros eventos e análise.',
                'position' => 110,
                'completed_at' => null,
            ],
            [
                'title' => 'Painel de incidentes e jogo de treino operacional',
                'area' => 'Observabilidade e operação',
                'status' => 'pending',
                'priority' => 'media',
                'description' => 'Implementar status page, runbooks e simulações mensais para reduzir tempo de resposta em incidentes reais.',
                'position' => 120,
                'completed_at' => null,
            ],
            [
                'title' => 'Estratégia de APIs públicas para parceiros',
                'area' => 'Evolução de plataforma',
                'status' => 'pending',
                'priority' => 'media',
                'description' => 'Publicar contratos claros, autenticação por aplicativo e cota de uso para expansão B2B sem risco de abuso.',
                'position' => 130,
                'completed_at' => null,
            ],
            [
                'title' => 'Plano de internacionalização e localização premium',
                'area' => 'Produto e UX',
                'status' => 'pending',
                'priority' => 'media',
                'description' => 'Suporte multiplataforma, tradução, fuso/localização de datas e suporte multilíngue com consistência de design.',
                'position' => 140,
                'completed_at' => null,
            ],
            [
                'title' => 'Programa de sucesso do cliente e retenção',
                'area' => 'Crescimento',
                'status' => 'pending',
                'priority' => 'media',
                'description' => 'Mapear marcos críticos de retenção, campanhas de reativação e mecanismos de upgrade com valor percebido.',
                'position' => 150,
                'completed_at' => null,
            ],
            [
                'title' => 'Escala de infraestrutura e custo previsível',
                'area' => 'Escalabilidade',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Separar workloads críticos, aplicar caching estratégico e criar alertas de custo por componente.',
                'position' => 160,
                'completed_at' => null,
            ],
            [
                'title' => 'Modelo de assinatura com valor percebido por uso',
                'area' => 'Monetização',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Ajustar planos com teto claro de previsibilidade, limites justos e upsell transparente sem surpresas no checkout.',
                'position' => 170,
                'completed_at' => null,
            ],
            [
                'title' => 'Certificação GitHub e prova de maturidade de produto',
                'area' => 'Go-live',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Completar dependências de lançamento, evidências e documentação final para submissão com chance máxima de aprovação.',
                'position' => 180,
                'completed_at' => null,
            ],
            [
                'title' => 'Lançamento público com monitoramento 24/7',
                'area' => 'Go-live',
                'status' => 'pending',
                'priority' => 'alta',
                'description' => 'Ativar rotina de monitoria, guardrails de segurança e comunicação pública diária durante 30 dias de primeira semana.',
                'position' => 190,
                'completed_at' => null,
            ],
        ];

        foreach ($items as $item) {
            RoadmapItem::updateOrCreate(
                ['title' => $item['title']],
                $item
            );
        }
    }

    private function cleanDemoData(string $email): void
    {
        $roadmapTitles = [
            'Cenario demo operacional',
            'Manifesto de produto e trilha de impacto',
            'Arquitetura técnica de referência',
            'Roteiro de dados e privacidade por default',
            'Roadmap visual e governança de prioridades',
            'Métricas de produto com decisões acionáveis',
            'MVP administrativo com acessibilidade e clareza',
            'Fluxo de autenticação robusto e proteção antifraude',
            'Hardening de webhooks e tolerância a falhas',
            'Catálogo de qualidade de código e revisão contínua',
            'Camada de eventos AI de baixa latência',
            'Roteiro de onboarding inteligente',
            'Painel de incidentes e jogo de treino operacional',
            'Estratégia de APIs públicas para parceiros',
            'Plano de internacionalização e localização premium',
            'Programa de sucesso do cliente e retenção',
            'Escala de infraestrutura e custo previsível',
            'Modelo de assinatura com valor percebido por uso',
            'Certificação GitHub e prova de maturidade de produto',
            'Lançamento público com monitoramento 24/7',
        ];

        $workspace = Workspace::where('slug', 'workspace-demo')->first();
        if ($workspace) {
            $workspace->delete();
        }

        User::where('email', $email)->delete();
        BillingPlan::where('slug', 'growth-demo')->delete();
        KnowledgeBaseArticle::whereIn('slug', collect(KnowledgeBaseCatalog::articles())->pluck('slug'))->delete();
        RoadmapItem::whereIn('title', $roadmapTitles)->delete();
    }
}
