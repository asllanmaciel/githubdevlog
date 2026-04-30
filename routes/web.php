<?php

use App\Models\BillingPlan;
use App\Models\BillingEvent;
use App\Models\GithubInstallation;
use App\Models\KnowledgeBaseArticle;
use App\Models\Notification;
use App\Models\RoadmapItem;
use App\Models\SecretRotation;
use App\Models\SupportTicket;
use App\Models\UsageInvoice;
use App\Models\WebhookEvent;
use App\Models\WebhookEventNote;
use App\Models\WebhookEventTask;
use App\Models\Workspace;
use App\Models\WorkspaceInvite;
use App\Models\WorkspaceMember;
use App\Models\WorkspaceSubscription;
use App\Services\MercadoPagoBillingService;
use App\Support\AuditTrail;
use App\Support\SystemHealth;
use App\Support\SupportSla;
use App\Support\SubscriptionLifecycle;
use App\Support\WebhookSanitizer;
use App\Support\WorkspaceAccess;
use App\Support\WorkspaceInviteDelivery;
use App\Support\WorkspaceUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$workspacePlan = fn (Workspace $workspace): ?BillingPlan => WorkspaceUsage::plan($workspace);
$workspaceUsage = fn (Workspace $workspace): int => WorkspaceUsage::usageThisMonth($workspace);
$workspaceLimitReached = fn (Workspace $workspace): bool => WorkspaceUsage::limitReached($workspace);

Route::get('/', fn () => view('landing'))->name('home');
Route::get('/status', fn () => view('status'))->name('status');
Route::get('/health', function () {
    $report = SystemHealth::report();

    return response()->json($report, $report['ok'] ? 200 : 503);
})->name('health');
Route::get('/docs/usuarios', fn () => view('docs.users'))->name('docs.users');
Route::get('/docs/api', fn () => view('docs.api'))->name('docs.api');
Route::get('/docs/admin', fn () => redirect('/admin/docs'))->name('docs.admin');
Route::get('/github', fn () => view('github'))->name('github');
Route::get('/contact', fn () => view('contact'))->name('contact');
Route::get('/changelog', fn () => view('changelog', [
    'entries' => \App\Support\PublicChangelog::entries(),
]))->name('changelog');
Route::get('/pricing', function () {
    $plans = BillingPlan::where('active', true)
        ->orderBy('price_cents')
        ->orderBy('monthly_event_limit')
        ->get();

    return view('pricing', compact('plans'));
})->name('pricing');
Route::get('/privacy', fn () => view('legal.privacy'))->name('privacy');
Route::get('/terms', fn () => view('legal.terms'))->name('terms');
Route::get('/security', fn () => view('legal.security'))->name('security');
Route::get('/sitemap.xml', function () {
    $urls = collect([
        route('home'),        route('changelog'),
        route('contact'),
        route('github'),
        route('docs.users'),
        route('docs.api'),
        route('status'),
        route('security'),
        route('privacy'),
        route('terms'),
        route('login'),
        route('register'),
    ]);

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::get('/robots.txt', function () {
    return response(
        "User-agent: *\n".
        "Allow: /\n".
        "Disallow: /admin\n".
        "Disallow: /dashboard\n".
        "Sitemap: ".route('sitemap')."\n",
        200,
        ['Content-Type' => 'text/plain'],
    );
})->name('robots');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('auth', ['mode' => 'login']))->name('login');
    Route::get('/register', fn () => view('auth', ['mode' => 'register']))->name('register');

    Route::post('/register', function (Request $request) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'workspace' => ['required', 'string', 'max:120'],
        ]);

        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $data['password'],
        ]);

        $workspace = Workspace::create([
            'uuid' => (string) Str::uuid(),
            'name' => $data['workspace'],
            'slug' => Str::slug($data['workspace']).'-'.Str::lower(Str::random(5)),
            'webhook_secret' => 'dlog_'.Str::random(48),
        ]);

        $workspace->members()->create(['user_id' => $user->id, 'role' => 'owner']);

        WorkspaceInvite::where('email', $user->email)
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->get()
            ->each(function (WorkspaceInvite $invite) use ($user) {
                WorkspaceMember::updateOrCreate(
                    ['workspace_id' => $invite->workspace_id, 'user_id' => $user->id],
                    ['role' => $invite->role],
                );
                $invite->update(['status' => 'accepted', 'accepted_at' => now()]);
            });

        Auth::login($user);

        return redirect()->route('dashboard');
    })->name('register.store');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);

        if (! Auth::attempt(['email' => strtolower($credentials['email']), 'password' => $credentials['password']], true)) {
            return back()->withErrors(['email' => 'Email ou senha invalidos.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return Auth::user()->is_super_admin
            ? redirect('/admin')
            : redirect()->route('dashboard');
    })->name('login.store');
});

Route::middleware('auth')->group(function () use ($workspaceLimitReached) {
    Route::get('/dashboard', function () {
        if (Auth::user()->is_super_admin) {
            return redirect('/admin');
        }

        $workspace = Auth::user()->workspaces()->first();
        $events = $workspace ? $workspace->webhookEvents()->latest()->limit(50)->get() : collect();
        $notifications = $workspace ? Notification::where('workspace_id', $workspace->id)->latest()->limit(5)->get() : collect();
        $githubInstallation = $workspace ? $workspace->githubInstallations()->latest()->first() : null;
        $members = $workspace ? $workspace->members()->with('user')->get() : collect();
        $invites = $workspace ? $workspace->invites()->latest()->limit(10)->get() : collect();
        $canManageWorkspace = $workspace ? WorkspaceAccess::canManage(Auth::user(), $workspace) : false;
        $canManageBilling = $workspace ? WorkspaceAccess::can(Auth::user(), $workspace, 'manage_billing') : false;
        $canManageSecrets = $workspace ? WorkspaceAccess::can(Auth::user(), $workspace, 'manage_secrets') : false;
        $canManageGitHub = $workspace ? WorkspaceAccess::can(Auth::user(), $workspace, 'manage_github_app') : false;
        $canCreateTestEvents = $workspace ? WorkspaceAccess::can(Auth::user(), $workspace, 'create_test_events') : false;
        $canAnnotateEvents = $workspace ? WorkspaceAccess::can(Auth::user(), $workspace, 'annotate_events') : false;
        $workspaceRole = $workspace ? WorkspaceAccess::currentRole(Auth::user(), $workspace) : null;
        $permissionMatrix = WorkspaceAccess::roleMatrix();
        $permissionLabels = WorkspaceAccess::labels();

        return view('dashboard', compact('workspace', 'events', 'notifications', 'githubInstallation', 'members', 'invites', 'canManageWorkspace', 'canManageBilling', 'canManageSecrets', 'canManageGitHub', 'canCreateTestEvents', 'canAnnotateEvents', 'workspaceRole', 'permissionMatrix', 'permissionLabels'));
    })->name('dashboard');


    Route::post('/invites/{token}/accept', function (string $token) {
        $invite = WorkspaceInvite::where('token', $token)->with('workspace')->firstOrFail();
        abort_unless($invite->isAcceptable(), 422, 'Convite indisponivel.');
        abort_unless(strtolower(Auth::user()->email) === strtolower($invite->email), 403);

        WorkspaceMember::updateOrCreate(
            ['workspace_id' => $invite->workspace_id, 'user_id' => Auth::id()],
            ['role' => $invite->role],
        );
        $invite->update(['status' => 'accepted', 'accepted_at' => now()]);
        AuditTrail::record('workspace.invite.accepted', $invite, $invite->workspace, ['email' => $invite->email, 'role' => $invite->role]);

        return redirect()->route('dashboard')->with('status', 'Convite aceito. Workspace vinculado ao seu usuario.');
    })->name('workspace.invites.accept');

    Route::post('/notifications/{notification}/read', function (Notification $notification) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        abort_unless($notification->workspace_id === $workspace->id, 403);

        $notification->update(['read_at' => now()]);

        return redirect()->route('dashboard')->with('status', 'Notificacao marcada como lida.');
    })->name('notifications.read');

    Route::post('/notifications/read-all', function () {
        $workspace = Auth::user()->workspaces()->firstOrFail();

        Notification::where('workspace_id', $workspace->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('dashboard')->with('status', 'Notificacoes marcadas como lidas.');
    })->name('notifications.read-all');
    Route::post('/workspace/members/invite', function (Request $request) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        abort_unless(WorkspaceAccess::can(Auth::user(), $workspace, 'manage_members'), 403);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:180'],
            'role' => ['required', 'string', 'in:admin,developer,viewer'],
        ]);

        $result = WorkspaceAccess::invite($workspace, Auth::user(), $data['email'], $data['role']);
        $delivery = null;

        if (($result['status'] ?? null) === 'invite_pending' && isset($result['invite'])) {
            $delivery = WorkspaceInviteDelivery::send($result['invite']);
        }

        AuditTrail::record('workspace.member.invited', $workspace, $workspace, [
            'email' => strtolower($data['email']),
            'role' => $data['role'],
            'status' => $result['status'],
            'invite_sent' => $delivery['sent'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('status', $result['status'] === 'member_added'
            ? 'Membro adicionado ao workspace.'
            : (($delivery['sent'] ?? false)
                ? 'Convite enviado por email e mantido pendente ate o aceite.'
                : 'Convite pendente criado. Envio de email indisponivel; use o link do convite no painel.'));
    })->name('workspace.members.invite');

    Route::post('/workspace/members/{member}/remove', function (WorkspaceMember $member) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        abort_unless($member->workspace_id === $workspace->id, 403);
        abort_unless(WorkspaceAccess::can(Auth::user(), $workspace, 'manage_members'), 403);
        abort_if($member->role === 'owner', 422, 'Owner nao pode ser removido por esta acao.');

        $removedUserId = $member->user_id;
        $member->delete();
        AuditTrail::record('workspace.member.removed', $workspace, $workspace, ['removed_user_id' => $removedUserId]);

        return redirect()->route('dashboard')->with('status', 'Membro removido do workspace.');
    })->name('workspace.members.remove');

    Route::post('/workspace/invites/{invite}/cancel', function (WorkspaceInvite $invite) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        abort_unless($invite->workspace_id === $workspace->id, 403);
        abort_unless(WorkspaceAccess::can(Auth::user(), $workspace, 'manage_members'), 403);

        $invite->update(['status' => 'canceled']);
        AuditTrail::record('workspace.invite.canceled', $invite, $workspace, ['email' => $invite->email]);

        return redirect()->route('dashboard')->with('status', 'Convite cancelado.');
    })->name('workspace.invites.cancel');

    Route::post('/workspace/secret/rotate', function () {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        $workspace->update([
            'webhook_secret' => 'dlog_'.Str::random(48),
            'webhook_secret_rotated_at' => now(),
        ]);

        $rotation = SecretRotation::create([
            'workspace_id' => $workspace->id,
            'user_id' => Auth::id(),
            'secret_type' => 'workspace_webhook_secret',
            'rotated_by' => 'user_dashboard',
            'metadata' => ['workspace' => $workspace->name],
            'rotated_at' => now(),
        ]);

        AuditTrail::record('workspace.secret.rotated', $rotation, $workspace, [
            'secret_type' => 'workspace_webhook_secret',
        ]);

        return redirect()->route('dashboard')->with('status', 'Secret rotacionado. Atualize o webhook no GitHub.');
    })->name('workspace.secret.rotate');

    Route::post('/dashboard/test-event', function (Request $request) use ($workspaceLimitReached) {
        $workspace = Auth::user()->workspaces()->firstOrFail();

        if ($workspaceLimitReached($workspace)) {
            return back()->withErrors(['payload' => 'Limite mensal de eventos atingido para este workspace. Faca upgrade do plano ou aguarde a proxima janela mensal.']);
        }

        $payload = json_decode($request->input('payload', '{}'), true);

        if (! is_array($payload)) return back()->withErrors(['payload' => 'Payload JSON invalido.']);

        $event = $workspace->webhookEvents()->create([
            'source' => 'manual-test', 'event_name' => $payload['event'] ?? 'push', 'action' => $payload['action'] ?? null,
            'delivery_id' => null, 'signature_valid' => true, 'validation_method' => 'authenticated-session',
            'headers' => ['x-devlog-test' => 'true'], 'payload' => WebhookSanitizer::clean($payload), 'received_at' => now(),
        ]);

        AuditTrail::record('webhook.test_event.created', $event, $workspace, ['event_name' => $event->event_name]);

        return redirect()->route('dashboard')->with('status', 'Evento de teste salvo no workspace.');
    })->name('dashboard.test-event');

    Route::post('/billing/checkout/{plan}', function (BillingPlan $plan, MercadoPagoBillingService $billing) {
        $workspace = Auth::user()->workspaces()->firstOrFail();

        if (! $plan->active || $plan->price_cents <= 0) {
            return redirect()->route('dashboard')->withErrors([
                'billing' => 'Plano indisponivel para checkout.',
            ]);
        }

        try {
            $preference = $billing->createCheckoutPreference($workspace, $plan, Auth::user()->email);
            $checkoutUrl = $billing->checkoutUrl($preference);
        } catch (\Throwable $exception) {
            return redirect()->route('dashboard')->withErrors([
                'billing' => $exception->getMessage(),
            ]);
        }

        if (! $checkoutUrl) {
            return redirect()->route('dashboard')->withErrors([
                'billing' => 'Mercado Pago nao retornou URL de checkout.',
            ]);
        }

        WorkspaceSubscription::updateOrCreate(
            ['workspace_id' => $workspace->id],
            [
                'billing_plan_id' => $plan->id,
                'provider' => 'mercado_pago',
                'provider_reference' => $preference->id ?? null,
                'status' => 'pending',
                'trial_ends_at' => null,
                'current_period_ends_at' => now()->addMonth(),
            ],
        );

        Notification::create([
            'workspace_id' => $workspace->id,
            'user_id' => Auth::id(),
            'title' => 'Checkout Mercado Pago iniciado',
            'body' => 'Preferencia '.($preference->id ?? 'sem-id').' criada para o plano '.$plan->name.'.',
            'type' => 'billing',
        ]);

        AuditTrail::record('billing.checkout.started', $plan, $workspace, ['plan' => $plan->slug, 'preference_id' => $preference->id ?? null]);

        return redirect()->away($checkoutUrl);
    })->name('billing.checkout');


    Route::post('/billing/subscription/cancel', function (Request $request) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:240'],
        ]);

        $subscription = SubscriptionLifecycle::cancel($workspace, Auth::user(), $data['reason'] ?? null);

        if (! $subscription) {
            return redirect()->route('dashboard')->withErrors(['billing' => 'Nenhuma assinatura encontrada para cancelar.']);
        }

        return redirect()->route('dashboard')->with('status', 'Assinatura cancelada no painel. Se houver recorrencia externa ativa, revise tambem o Mercado Pago.');
    })->name('billing.subscription.cancel');

    Route::get('/billing/return', function (Request $request) {
        $status = (string) $request->query('status', 'pending');

        return redirect()->route('dashboard')->with(
            'status',
            $status === 'success'
                ? 'Pagamento recebido pelo Mercado Pago. A assinatura sera confirmada pelo webhook.'
                : 'Retorno Mercado Pago: '.$status.'. Vamos aguardar a confirmacao do webhook.'
        );
    })->name('billing.return');

    Route::get('/github/install', function (Request $request) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        $setupUrl = config('services.github_app.setup_url');

        if (! filled($setupUrl) || str_contains($setupUrl, 'your-github-app-slug')) {
            return redirect()->route('dashboard')->withErrors([
                'github' => 'Configure GITHUB_APP_SETUP_URL com a URL oficial de instalaÃ§Ã£o do GitHub App.',
            ]);
        }

        $state = Str::random(48);
        $request->session()->put('github_app_install_state', $state);
        $request->session()->put('github_app_install_workspace_id', $workspace->id);

        return redirect()->away($setupUrl.(str_contains($setupUrl, '?') ? '&' : '?').http_build_query([
            'state' => $state,
        ]));
    })->name('github.install');

    Route::get('/github/callback', function (Request $request) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        $sessionWorkspaceId = (int) $request->session()->pull('github_app_install_workspace_id');
        $sessionState = (string) $request->session()->pull('github_app_install_state');
        $state = (string) $request->query('state', '');
        $installationId = (string) $request->query('installation_id', '');

        if ($sessionWorkspaceId !== $workspace->id || $sessionState === '' || ! hash_equals($sessionState, $state)) {
            return redirect()->route('dashboard')->withErrors([
                'github' => 'NÃ£o foi possÃ­vel validar o retorno da instalaÃ§Ã£o GitHub. Tente iniciar a instalaÃ§Ã£o novamente.',
            ]);
        }

        if ($installationId === '') {
            return redirect()->route('dashboard')->withErrors([
                'github' => 'O GitHub nÃ£o retornou installation_id. Confirme se a instalaÃ§Ã£o foi concluÃ­da.',
            ]);
        }

        GithubInstallation::updateOrCreate(
            ['workspace_id' => $workspace->id, 'installation_id' => $installationId],
            [
                'account_login' => $request->query('account_login'),
                'account_type' => $request->query('account_type'),
                'permissions' => [],
                'events' => [],
                'installed_at' => now(),
            ],
        );

        $workspace->update(['github_app_installation_id' => $installationId]);

        Notification::create([
            'workspace_id' => $workspace->id,
            'title' => 'GitHub App instalado',
            'body' => 'A instalaÃ§Ã£o '.$installationId.' foi vinculada ao workspace.',
            'type' => 'github',
        ]);

        return redirect()->route('dashboard')->with('status', 'GitHub App instalado e vinculado ao workspace.');
    })->name('github.callback');

    Route::post('/events/{event}/notes', function (WebhookEvent $event, Request $request) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        abort_unless($event->workspace_id === $workspace->id, 403);
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);
        $note = WebhookEventNote::create(['webhook_event_id' => $event->id, 'user_id' => Auth::id(), 'body' => $data['body']]);
        AuditTrail::record('webhook.note.created', $event, $workspace, ['note_id' => $note->id]);

        return redirect()->route('dashboard')->with('status', 'Nota adicionada ao evento.');
    })->name('events.notes.store');

    Route::post('/events/{event}/tasks', function (WebhookEvent $event, Request $request) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        abort_unless($event->workspace_id === $workspace->id, 403);
        $data = $request->validate(['title' => ['required', 'string', 'max:180']]);
        $task = WebhookEventTask::create(['webhook_event_id' => $event->id, 'title' => $data['title'], 'status' => 'open']);
        AuditTrail::record('webhook.task.created', $event, $workspace, ['task_id' => $task->id]);

        return redirect()->route('dashboard')->with('status', 'Tarefa criada a partir do webhook.');
    })->name('events.tasks.store');

    Route::get('/support', function () {
        $articles = KnowledgeBaseArticle::where('published', true)
            ->orderBy('position')
            ->orderBy('title')
            ->get();

        return view('support', compact('articles'));
    })->name('support');

    Route::get('/help', function () {
        $articles = KnowledgeBaseArticle::where('published', true)
            ->orderBy('position')
            ->orderBy('title')
            ->get();

        return view('support', compact('articles'));
    })->name('help');

    Route::post('/support', function (Request $request) {
        $workspace = Auth::user()->workspaces()->first();
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:160'],
            'category' => ['required', 'string', 'in:technical,billing,github_app,account,security'],
            'priority' => ['required', 'string', 'in:low,normal,high,urgent'],
            'message' => ['required', 'string', 'max:4000'],
        ]);
        $sla = SupportSla::apply($data['priority']);
        $ticket = SupportTicket::create([
            ...$data,
            ...$sla,
            'workspace_id' => $workspace?->id,
            'user_id' => Auth::id(),
            'status' => 'open',
        ]);
        AuditTrail::record('support.ticket.created', $ticket, $workspace, [
            'subject' => $ticket->subject,
            'category' => $ticket->category,
            'priority' => $ticket->priority,
        ]);

        return redirect()->route('support')->with('status', 'Chamado aberto com SLA de primeira resposta. Vamos acompanhar pelo painel admin.');
    })->name('support.store');

    Route::get('/admin-roadmap', function () {
        abort_unless(Auth::user()->is_super_admin, 403);

        return redirect('/admin/roadmap');
    })->name('admin.roadmap.dashboard');

    Route::post('/admin/roadmap/{item}/toggle', function (RoadmapItem $item) {
        abort_unless(Auth::user()->is_super_admin, 403);
        $item->update([
            'status' => $item->status === 'done' ? 'pending' : 'done',
            'completed_at' => $item->status === 'done' ? null : now(),
        ]);

        return redirect('/admin/roadmap')->with('status', 'Roadmap atualizado.');
    })->name('admin.roadmap.toggle');

    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    })->name('logout');
});

Route::post('/webhooks/github/{workspaceUuid}', function (Request $request, string $workspaceUuid) use ($workspacePlan, $workspaceUsage, $workspaceLimitReached) {
    $workspace = Workspace::where('uuid', $workspaceUuid)->firstOrFail();
    $rawBody = $request->getContent();
    $signature = (string) $request->header('X-Hub-Signature-256', '');
    $expected = 'sha256='.hash_hmac('sha256', $rawBody, $workspace->webhook_secret);
    $validSignature = $signature !== '' && hash_equals($expected, $signature);

    if (! $validSignature) return response()->json(['error' => 'Assinatura GitHub invalida.'], 401);

    if ($workspaceLimitReached($workspace)) {
        $plan = $workspacePlan($workspace);
        $limit = max((int) ($plan?->monthly_event_limit ?? 1000), 1);
        $usage = $workspaceUsage($workspace);

        WorkspaceUsage::notifyLimitReached($workspace);

        return response()->json([
            'error' => 'Limite mensal de eventos atingido.',
            'usage' => $usage,
            'limit' => $limit,
        ], 429);
    }

    $payload = json_decode($rawBody, true) ?: [];
    $event = $workspace->webhookEvents()->create([
        'source' => 'github', 'event_name' => (string) $request->header('X-GitHub-Event', 'github'),
        'action' => $payload['action'] ?? null, 'delivery_id' => $request->header('X-GitHub-Delivery'),
        'signature_valid' => true, 'validation_method' => 'x-hub-signature-256',
        'headers' => WebhookSanitizer::clean(collect($request->headers->all())->map(fn ($value) => $value[0] ?? null)->all()),
        'payload' => WebhookSanitizer::clean($payload), 'received_at' => now(),
    ]);

    Notification::create([
        'workspace_id' => $workspace->id,
        'title' => 'Novo evento '.$event->event_name,
        'body' => data_get($payload, 'repository.full_name', 'GitHub').' enviou um webhook validado.',
        'type' => 'webhook',
    ]);

    return response()->json(['ok' => true, 'id' => $event->id]);
})->name('webhooks.github');

Route::post('/webhooks/github-app', function (Request $request) use ($workspacePlan, $workspaceUsage, $workspaceLimitReached) {
    $rawBody = $request->getContent();
    $secret = (string) config('services.github_app.webhook_secret');

    if ($secret === '') {
        return response()->json(['error' => 'GitHub App webhook secret nao configurado.'], 503);
    }

    $signature = (string) $request->header('X-Hub-Signature-256', '');
    $expected = 'sha256='.hash_hmac('sha256', $rawBody, $secret);

    if ($signature === '' || ! hash_equals($expected, $signature)) {
        return response()->json(['error' => 'Assinatura GitHub App invalida.'], 401);
    }

    $payload = json_decode($rawBody, true) ?: [];
    $installationId = (string) data_get($payload, 'installation.id', '');

    if ($installationId === '') {
        return response()->json(['error' => 'Payload sem installation.id.'], 422);
    }

    $installation = GithubInstallation::where('installation_id', $installationId)->first();

    if (! $installation) {
        return response()->json(['error' => 'Instalacao GitHub App nao vinculada a workspace.'], 404);
    }

    $workspace = $installation->workspace;

    if (! $workspace) {
        return response()->json(['error' => 'Workspace da instalacao nao encontrado.'], 404);
    }

    if ($workspaceLimitReached($workspace)) {
        $plan = $workspacePlan($workspace);
        $limit = max((int) ($plan?->monthly_event_limit ?? 1000), 1);
        $usage = $workspaceUsage($workspace);

        WorkspaceUsage::notifyLimitReached($workspace);

        return response()->json([
            'error' => 'Limite mensal de eventos atingido.',
            'usage' => $usage,
            'limit' => $limit,
        ], 429);
    }

    $event = $workspace->webhookEvents()->create([
        'source' => 'github-app',
        'event_name' => (string) $request->header('X-GitHub-Event', 'github_app'),
        'action' => $payload['action'] ?? null,
        'delivery_id' => $request->header('X-GitHub-Delivery'),
        'signature_valid' => true,
        'validation_method' => 'github-app-x-hub-signature-256',
        'headers' => WebhookSanitizer::clean(collect($request->headers->all())->map(fn ($value) => $value[0] ?? null)->all()),
        'payload' => WebhookSanitizer::clean($payload),
        'received_at' => now(),
    ]);

    $installation->update([
        'account_login' => data_get($payload, 'installation.account.login', $installation->account_login),
        'account_type' => data_get($payload, 'installation.account.type', $installation->account_type),
        'events' => array_values(array_unique(array_filter([...(array) $installation->events, $event->event_name]))),
    ]);

    Notification::create([
        'workspace_id' => $workspace->id,
        'title' => 'Novo evento GitHub App '.$event->event_name,
        'body' => data_get($payload, 'repository.full_name', data_get($payload, 'organization.login', 'GitHub App')).' enviou um evento validado.',
        'type' => 'webhook',
    ]);

    return response()->json(['ok' => true, 'id' => $event->id]);
})->name('webhooks.github-app');

Route::get('/webhooks/mercado-pago', function () {
    return response()->json([
        'ok' => true,
        'provider' => 'mercado_pago',
        'method' => 'GET',
        'message' => 'Endpoint ativo. Configure o Mercado Pago para enviar notificacoes via POST.',
    ]);
})->name('webhooks.mercado-pago.health');

Route::post('/webhooks/mercado-pago', function (Request $request, MercadoPagoBillingService $billing) {
    $signatureValidation = $billing->validateWebhookSignature($request);

    if (! $signatureValidation['configured']) {
        return response()->json([
            'error' => 'Webhook Mercado Pago sem secret configurado.',
        ], 503);
    }

    if (! $signatureValidation['valid']) {
        return response()->json([
            'error' => 'Assinatura Mercado Pago invalida.',
            'reason' => $signatureValidation['reason'],
        ], 401);
    }

    $payload = $request->all();
    $preferenceId = (string) (
        data_get($payload, 'data.id')
        ?? data_get($payload, 'id')
        ?? data_get($payload, 'preference_id')
        ?? ''
    );
    $eventType = (string) ($request->query('type') ?? data_get($payload, 'type') ?? data_get($payload, 'topic') ?? 'mercado_pago');
    $status = (string) (data_get($payload, 'status') ?? data_get($payload, 'action') ?? 'received');
    $providerEventId = implode(':', array_filter([
        (string) $request->header('x-request-id'),
        (string) (data_get($payload, 'id') ?? ''),
        $eventType,
        $preferenceId,
    ])) ?: (string) Str::uuid();
    $billingEvent = BillingEvent::firstOrCreate(
        ['provider' => 'mercado_pago', 'provider_event_id' => $providerEventId],
        [
            'request_id' => $request->header('x-request-id'),
            'event_type' => $eventType,
            'action' => data_get($payload, 'action'),
            'resource_id' => $preferenceId,
            'status' => 'received',
            'signature_valid' => true,
            'payload' => WebhookSanitizer::clean($payload),
        ],
    );

    if (! $billingEvent->wasRecentlyCreated && $billingEvent->processed_at) {
        return response()->json([
            'ok' => true,
            'duplicate' => true,
            'billing_event_id' => $billingEvent->id,
        ]);
    }

    $payment = null;
    $externalReference = null;
    $workspaceId = null;
    $billingPlanId = null;
    $usageInvoiceId = null;

    if ($preferenceId === '') {
        $billingEvent->update([
            'status' => 'ignored',
            'error_message' => 'Evento Mercado Pago recebido sem referencia direta.',
            'processed_at' => now(),
        ]);

        return response()->json(['ok' => true, 'message' => 'Evento Mercado Pago recebido sem referencia direta.']);
    }

    if (str_contains($eventType, 'payment') && ctype_digit($preferenceId) && $billing->isConfigured()) {
        try {
            $payment = $billing->getPayment($preferenceId);
            $status = (string) ($payment->status ?? $status);
            $externalReference = $payment->external_reference ?? null;
            $parsed = $billing->parseExternalReference($externalReference);
            $workspaceId = $parsed['workspace_id'];
            $billingPlanId = $parsed['billing_plan_id'];
            $usageInvoiceId = $parsed['usage_invoice_id'];
        } catch (\Throwable $exception) {
            $billingEvent->update([
                'status' => 'pending_lookup',
                'error_message' => app()->isLocal() ? $exception->getMessage() : 'Pagamento ainda nao pode ser consultado.',
                'processed_at' => now(),
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Evento recebido, mas o pagamento ainda nao pode ser consultado.',
                'detail' => app()->isLocal() ? $exception->getMessage() : null,
            ]);
        }
    }

    if ($usageInvoiceId) {
        $invoice = UsageInvoice::find($usageInvoiceId);

        if (! $invoice) {
            $billingEvent->update([
                'status' => 'usage_invoice_unmatched',
                'error_message' => 'Fatura de uso informada no external_reference nao foi encontrada.',
                'processed_at' => now(),
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Fatura de uso nao encontrada.',
                'billing_event_id' => $billingEvent->id,
            ]);
        }

        $approved = str_contains($status, 'approved') || str_contains($status, 'paid');
        $invoice->update([
            'status' => $approved ? 'paid' : 'issued',
            'provider' => 'mercado_pago',
            'provider_reference' => $payment?->id ?? $invoice->provider_reference ?? $preferenceId,
            'paid_at' => $approved ? now() : $invoice->paid_at,
            'metadata' => array_merge($invoice->metadata ?? [], [
                'last_webhook_status' => $status,
                'last_billing_event_id' => $billingEvent->id,
            ]),
        ]);

        $billingEvent->update([
            'workspace_id' => $invoice->workspace_id,
            'billing_plan_id' => $invoice->billing_plan_id,
            'resource_id' => $payment?->id ?? $preferenceId,
            'external_reference' => $externalReference,
            'status' => $approved ? 'usage_invoice_paid' : 'usage_invoice_pending',
            'processed_at' => now(),
        ]);

        Notification::create([
            'workspace_id' => $invoice->workspace_id,
            'title' => $approved ? 'Fatura de excedente paga' : 'Fatura de excedente atualizada',
            'body' => 'Fatura de uso '.$invoice->period.' recebeu status '.$status.' via Mercado Pago.',
            'type' => 'billing',
        ]);

        return response()->json([
            'ok' => true,
            'status' => $invoice->status,
            'usage_invoice_id' => $invoice->id,
            'billing_event_id' => $billingEvent->id,
        ]);
    }

    $subscription = null;

    if ($workspaceId) {
        $subscription = WorkspaceSubscription::where('workspace_id', $workspaceId)->first();
    }

    $subscription ??= WorkspaceSubscription::where('provider', 'mercado_pago')
        ->where('provider_reference', $preferenceId)
        ->first();

    if (! $subscription) {
        if ($workspaceId && $billingPlanId) {
            $subscription = WorkspaceSubscription::create([
                'workspace_id' => $workspaceId,
                'billing_plan_id' => $billingPlanId,
                'provider' => 'mercado_pago',
                'provider_reference' => $payment?->id ?? $preferenceId,
                'status' => 'pending',
                'current_period_ends_at' => now()->addMonth(),
            ]);
        } else {
            $billingEvent->update([
                'status' => 'unmatched',
                'error_message' => 'Referencia Mercado Pago ainda nao associada a assinatura.',
                'processed_at' => now(),
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Referencia Mercado Pago ainda nao associada a assinatura.',
                'billing_event_id' => $billingEvent->id,
            ]);
        }
    }

    $approved = str_contains($status, 'approved') || str_contains($status, 'paid');
    $subscription->update([
        'billing_plan_id' => $billingPlanId ?: $subscription->billing_plan_id,
        'provider_reference' => $payment?->id ?? $subscription->provider_reference ?? $preferenceId,
        'status' => $approved ? 'active' : 'pending',
        'current_period_ends_at' => $approved ? now()->addMonth() : $subscription->current_period_ends_at,
    ]);

    $billingEvent->update([
        'workspace_id' => $subscription->workspace_id,
        'workspace_subscription_id' => $subscription->id,
        'billing_plan_id' => $billingPlanId ?: $subscription->billing_plan_id,
        'resource_id' => $payment?->id ?? $preferenceId,
        'external_reference' => $externalReference,
        'status' => $approved ? 'processed_active' : 'processed_pending',
        'processed_at' => now(),
    ]);

    Notification::create([
        'workspace_id' => $subscription->workspace_id,
        'title' => 'Webhook Mercado Pago recebido',
        'body' => 'Evento '.$eventType.' para referencia '.$preferenceId.' com status '.$status.'.',
        'type' => 'billing',
    ]);

    return response()->json([
        'ok' => true,
        'status' => $subscription->status,
        'workspace_id' => $subscription->workspace_id,
        'billing_event_id' => $billingEvent->id,
    ]);
})->name('webhooks.mercado-pago');



