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
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
                'overage_price_cents' => 2,
                'features' => [
                    'Workspace privado',
                    'Historico de webhooks GitHub',
                    'Validacao de assinatura',
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
        $articles = [
            [
                'title' => 'Como conectar um repositorio GitHub',
                'slug' => 'conectar-repositorio-github',
                'category' => 'Primeiros passos',
                'summary' => 'Configure o Payload URL, secret e eventos recomendados no GitHub.',
                'body' => "1. Abra Settings > Webhooks no repositorio.\n2. Cole o Payload URL do workspace.\n3. Escolha application/json.\n4. Cole o secret do workspace.\n5. Envie um ping e acompanhe no dashboard.",
                'position' => 1,
            ],
            [
                'title' => 'Por que meu webhook foi rejeitado?',
                'slug' => 'webhook-rejeitado',
                'category' => 'Troubleshooting',
                'summary' => 'Entenda falhas comuns de assinatura, endpoint e content type.',
                'body' => "Verifique se o secret no GitHub e no workspace sao iguais, se o content type e application/json e se a URL publica esta acessivel por HTTPS.",
                'position' => 2,
            ],
        ];

        foreach ($articles as $article) {
            KnowledgeBaseArticle::updateOrCreate(
                ['slug' => $article['slug']],
                $article + ['published' => true]
            );
        }
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
        RoadmapItem::updateOrCreate(
            ['title' => 'Cenario demo operacional'],
            [
                'area' => 'Lancamento',
                'status' => 'done',
                'priority' => 'alta',
                'description' => 'Seed demo cria usuario, workspace, plano, assinatura, eventos, suporte, notificacoes e artigos para apresentacao.',
                'position' => 10,
                'completed_at' => now(),
            ]
        );
    }

    private function cleanDemoData(string $email): void
    {
        $workspace = Workspace::where('slug', 'workspace-demo')->first();
        if ($workspace) {
            $workspace->delete();
        }

        User::where('email', $email)->delete();
        BillingPlan::where('slug', 'growth-demo')->delete();
        KnowledgeBaseArticle::whereIn('slug', ['conectar-repositorio-github', 'webhook-rejeitado'])->delete();
        RoadmapItem::where('title', 'Cenario demo operacional')->delete();
    }
}
