<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeployWebhookTest extends TestCase
{
    public function test_deploy_webhook_requires_secret_configuration(): void
    {
        config(['services.deploy_webhook.secret' => null]);

        $this->postJson('/webhooks/deploy/github', ['ref' => 'refs/heads/master'])
            ->assertStatus(503)
            ->assertJson(['error' => 'Deploy webhook nao configurado.']);
    }

    public function test_deploy_webhook_rejects_invalid_signature(): void
    {
        config(['services.deploy_webhook.secret' => 'secret-test']);

        $this->postJson('/webhooks/deploy/github', ['ref' => 'refs/heads/master'], [
            'X-GitHub-Event' => 'push',
            'X-Hub-Signature-256' => 'sha256=assinatura-invalida',
        ])->assertStatus(401);
    }

    public function test_deploy_webhook_ignores_other_branches_after_signature_validation(): void
    {
        config([
            'services.deploy_webhook.secret' => 'secret-test',
            'services.deploy_webhook.branch' => 'master',
        ]);

        $payload = json_encode(['ref' => 'refs/heads/main'], JSON_THROW_ON_ERROR);
        $signature = 'sha256='.hash_hmac('sha256', $payload, 'secret-test');

        $this->call('POST', '/webhooks/deploy/github', [], [], [], [
            'HTTP_X_GITHUB_EVENT' => 'push',
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payload)
            ->assertStatus(202)
            ->assertJson([
                'ok' => true,
                'ignored' => true,
                'reason' => 'branch_not_deployed',
            ]);
    }
}
