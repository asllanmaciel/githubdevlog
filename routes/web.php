<?php

use App\Models\BillingPlan;
use App\Models\Notification;
use App\Models\RoadmapItem;
use App\Models\SupportTicket;
use App\Models\WebhookEvent;
use App\Models\WebhookEventNote;
use App\Models\WebhookEventTask;
use App\Models\Workspace;
use App\Support\WebhookSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

$workspacePlan = function (Workspace $workspace): ?BillingPlan {
    return $workspace->subscription()->with('plan')->first()?->plan
        ?? BillingPlan::where('slug', 'free')->first();
};

$workspaceUsage = function (Workspace $workspace): int {
    return $workspace->webhookEvents()
        ->whereBetween('received_at', [now()->startOfMonth(), now()->endOfMonth()])
        ->count();
};

$workspaceLimitReached = function (Workspace $workspace) use ($workspacePlan, $workspaceUsage): bool {
    $plan = $workspacePlan($workspace);
    $limit = max((int) ($plan?->monthly_event_limit ?? 1000), 1);

    return $workspaceUsage($workspace) >= $limit;
};

Route::get('/', fn () => view('landing'))->name('home');
Route::get('/health', fn () => response()->json(['ok' => true, 'app' => config('app.name')]))->name('health');
Route::get('/docs/usuarios', fn () => view('docs.users'))->name('docs.users');
Route::get('/docs/admin', fn () => redirect('/admin/docs'))->name('docs.admin');
Route::get('/github', fn () => view('github'))->name('github');
Route::get('/privacy', fn () => view('legal.privacy'))->name('privacy');
Route::get('/terms', fn () => view('legal.terms'))->name('terms');

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
        Auth::login($user);

        return redirect()->route('dashboard');
    })->name('register.store');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);

        if (! Auth::attempt(['email' => strtolower($credentials['email']), 'password' => $credentials['password']], true)) {
            return back()->withErrors(['email' => 'Email ou senha inválidos.'])->onlyInput('email');
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

        return view('dashboard', compact('workspace', 'events', 'notifications'));
    })->name('dashboard');

    Route::post('/workspace/secret/rotate', function () {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        $workspace->update(['webhook_secret' => 'dlog_'.Str::random(48)]);

        return redirect()->route('dashboard')->with('status', 'Secret rotacionado. Atualize o webhook no GitHub.');
    })->name('workspace.secret.rotate');

    Route::post('/dashboard/test-event', function (Request $request) use ($workspaceLimitReached) {
        $workspace = Auth::user()->workspaces()->firstOrFail();

        if ($workspaceLimitReached($workspace)) {
            return back()->withErrors(['payload' => 'Limite mensal de eventos atingido para este workspace. Faça upgrade do plano ou aguarde a próxima janela mensal.']);
        }

        $payload = json_decode($request->input('payload', '{}'), true);

        if (! is_array($payload)) return back()->withErrors(['payload' => 'Payload JSON inválido.']);

        $workspace->webhookEvents()->create([
            'source' => 'manual-test', 'event_name' => $payload['event'] ?? 'push', 'action' => $payload['action'] ?? null,
            'delivery_id' => null, 'signature_valid' => true, 'validation_method' => 'authenticated-session',
            'headers' => ['x-devlog-test' => 'true'], 'payload' => WebhookSanitizer::clean($payload), 'received_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('status', 'Evento de teste salvo no workspace.');
    })->name('dashboard.test-event');

    Route::post('/events/{event}/notes', function (WebhookEvent $event, Request $request) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        abort_unless($event->workspace_id === $workspace->id, 403);
        $data = $request->validate(['body' => ['required', 'string', 'max:2000']]);
        WebhookEventNote::create(['webhook_event_id' => $event->id, 'user_id' => Auth::id(), 'body' => $data['body']]);

        return redirect()->route('dashboard')->with('status', 'Nota adicionada ao evento.');
    })->name('events.notes.store');

    Route::post('/events/{event}/tasks', function (WebhookEvent $event, Request $request) {
        $workspace = Auth::user()->workspaces()->firstOrFail();
        abort_unless($event->workspace_id === $workspace->id, 403);
        $data = $request->validate(['title' => ['required', 'string', 'max:180']]);
        WebhookEventTask::create(['webhook_event_id' => $event->id, 'title' => $data['title'], 'status' => 'open']);

        return redirect()->route('dashboard')->with('status', 'Tarefa criada a partir do webhook.');
    })->name('events.tasks.store');

    Route::get('/support', fn () => view('support'))->name('support');

    Route::post('/support', function (Request $request) {
        $workspace = Auth::user()->workspaces()->first();
        $data = $request->validate(['subject' => ['required', 'string', 'max:160'], 'message' => ['required', 'string', 'max:4000']]);
        SupportTicket::create([...$data, 'workspace_id' => $workspace?->id, 'user_id' => Auth::id(), 'priority' => 'normal']);

        return redirect()->route('support')->with('status', 'Chamado aberto. Vamos acompanhar pelo painel admin.');
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

    if (! $validSignature) return response()->json(['error' => 'Assinatura GitHub inválida.'], 401);

    if ($workspaceLimitReached($workspace)) {
        $plan = $workspacePlan($workspace);
        $limit = max((int) ($plan?->monthly_event_limit ?? 1000), 1);
        $usage = $workspaceUsage($workspace);

        Notification::create([
            'workspace_id' => $workspace->id,
            'title' => 'Limite mensal de webhooks atingido',
            'body' => 'O workspace atingiu '.$usage.'/'.$limit.' eventos no plano '.($plan?->name ?? 'Free').'. Novos eventos serão recusados até upgrade ou renovação mensal.',
            'type' => 'billing',
        ]);

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

