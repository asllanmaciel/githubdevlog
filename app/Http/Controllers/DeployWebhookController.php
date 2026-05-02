<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DeployWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $secret = (string) config('services.deploy_webhook.secret');
        $branch = (string) config('services.deploy_webhook.branch', 'master');
        $projectPath = (string) config('services.deploy_webhook.path', base_path());
        $delivery = (string) $request->header('X-GitHub-Delivery', 'sem-delivery');
        $logger = Log::channel('deploy');

        if ($secret === '') {
            $logger->warning('Deploy webhook sem secret configurado.', ['delivery' => $delivery]);

            return response()->json(['error' => 'Deploy webhook nao configurado.'], 503);
        }

        $rawBody = $request->getContent();
        $signature = (string) $request->header('X-Hub-Signature-256', '');
        $expected = 'sha256='.hash_hmac('sha256', $rawBody, $secret);

        if ($signature === '' || ! hash_equals($expected, $signature)) {
            $logger->warning('Assinatura invalida no deploy webhook.', ['delivery' => $delivery]);

            return response()->json(['error' => 'Assinatura invalida.'], 401);
        }

        if ($request->header('X-GitHub-Event') !== 'push') {
            $logger->info('Evento ignorado pelo deploy webhook.', [
                'delivery' => $delivery,
                'event' => $request->header('X-GitHub-Event'),
            ]);

            return response()->json(['ok' => true, 'ignored' => true, 'reason' => 'event_not_supported'], 202);
        }

        $payload = $request->json()->all();
        $ref = (string) ($payload['ref'] ?? '');
        $php = (string) config('services.deploy_webhook.php', 'php');

        if ($ref !== 'refs/heads/'.$branch) {
            $logger->info('Branch ignorada pelo deploy webhook.', [
                'delivery' => $delivery,
                'ref' => $ref,
                'branch' => $branch,
            ]);

            return response()->json(['ok' => true, 'ignored' => true, 'reason' => 'branch_not_deployed'], 202);
        }

        $logger->info('Deploy iniciado por webhook.', [
            'delivery' => $delivery,
            'branch' => $branch,
            'after' => $payload['after'] ?? null,
        ]);

        $commands = [
            ['git', 'pull', '--ff-only', 'origin', $branch],
            [$php, 'artisan', 'migrate', '--force'],
            [$php, 'artisan', 'devlog:sync-plans'],
            [$php, 'artisan', 'devlog:sync-roadmap'],
            [$php, 'artisan', 'devlog:sync-knowledge-base'],
            [$php, 'artisan', 'optimize:clear'],
            [$php, 'artisan', 'config:cache'],
            [$php, 'artisan', 'route:cache'],
            [$php, 'artisan', 'view:cache'],
        ];

        $results = [];

        foreach ($commands as $command) {
            $result = $this->run($command, $projectPath);
            $results[] = $result;

            if ($result['exit_code'] !== 0) {
                $logger->error('Deploy interrompido por falha.', [
                    'delivery' => $delivery,
                    'command' => $result['command'],
                    'exit_code' => $result['exit_code'],
                    'output' => $result['output'],
                ]);

                return response()->json([
                    'ok' => false,
                    'failed_command' => $result['command'],
                    'exit_code' => $result['exit_code'],
                ], 500);
            }
        }

        $logger->info('Deploy concluido por webhook.', [
            'delivery' => $delivery,
            'branch' => $branch,
            'after' => $payload['after'] ?? null,
        ]);

        return response()->json([
            'ok' => true,
            'branch' => $branch,
            'after' => $payload['after'] ?? null,
            'commands' => collect($results)->map(fn (array $result) => [
                'command' => $result['command'],
                'exit_code' => $result['exit_code'],
            ])->all(),
        ]);
    }

    private function run(array $command, string $projectPath): array
    {
        $process = new Process($command, $projectPath);
        $process->setTimeout((int) config('services.deploy_webhook.timeout', 180));
        $process->run();

        return [
            'command' => implode(' ', $command),
            'exit_code' => $process->getExitCode(),
            'output' => trim($process->getOutput()."\n".$process->getErrorOutput()),
        ];
    }
}
