<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Core\Env;
use App\Services\DevLogService;
use App\Services\GitHubService;
use Throwable;

final class GitHubWebhookController
{
    private readonly GitHubService $githubService;
    private readonly DevLogService $devLogService;

    public function __construct(
        ?GitHubService $githubService = null,
        ?DevLogService $devLogService = null,
    ) {
        $this->githubService = $githubService ?? new GitHubService();
        $this->devLogService = $devLogService ?? new DevLogService();
    }

    public function handle(): void
    {
        $payload = file_get_contents('php://input') ?: '';
        $headers = $this->getHeaders();
        $deliveryId = $this->getHeader($headers, 'X-GitHub-Delivery') ?? '';
        $event = $this->getHeader($headers, 'X-GitHub-Event');

        if (trim($payload) === '') {
            $this->jsonResponse(['error' => 'Payload vazio'], 400);
            return;
        }

        if (($event ?? '') !== 'push') {
            $this->jsonResponse([
                'status' => 'ignored',
                'reason' => 'evento_nao_suportado',
                'event' => $event ?: 'desconhecido',
            ], 200);
            return;
        }

        $secret = Env::get('GITHUB_WEBHOOK_SECRET', '');
        if (!$this->githubService->verifySignature($payload, $headers, $secret)) {
            $this->jsonResponse(['error' => 'Assinatura invalida'], 401);
            return;
        }

        try {
            $pushData = $this->githubService->parsePayload($payload);
            $result = $this->devLogService->processPushEvent($pushData, $deliveryId);
            $this->jsonResponse($result, 200);
        } catch (Throwable $e) {
            $this->jsonResponse([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function getHeaders(): array
    {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                return $headers;
            }
        }

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with((string) $key, 'HTTP_')) {
                $rawName = substr((string) $key, 5);
                $name = trim(strtolower(str_replace('_', '-', $rawName)), '-');
                $name = ucwords($name, '-');
                $name = str_replace(' ', '', $name);
                $headers[$name] = (string) $value;
            }
        }
        return $headers;
    }

    private function getHeader(array $headers, string $name): ?string
    {
        if (isset($headers[$name])) {
            return (string) $headers[$name];
        }
        $alt = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower((string) $key) === $alt) {
                return (string) $value;
            }
        }
        return null;
    }

    private function jsonResponse(array $data, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
