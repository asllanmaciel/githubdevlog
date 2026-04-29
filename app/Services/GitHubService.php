<?php

declare(strict_types=1);

namespace App\Services;

final class GitHubService
{
    public function verifySignature(string $payload, array $headers, string $secret): bool
    {
        if (trim($secret) === '') {
            return true;
        }

        $signature = $this->getHeader($headers, 'X-Hub-Signature-256');
        if ($signature === null || !str_starts_with($signature, 'sha256=')) {
            return false;
        }

        $signatureHash = substr($signature, 7);
        $expectedHash = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedHash, $signatureHash);
    }

    public function parsePayload(string $payload): array
    {
        $data = json_decode($payload, true);
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Payload invalido ou JSON malformado.');
        }

        if (($data['ref'] ?? null) === null) {
            throw new \InvalidArgumentException('Payload sem campo ref.');
        }

        return [
            'ref' => (string) $data['ref'],
            'before' => (string) ($data['before'] ?? ''),
            'after' => (string) ($data['after'] ?? ''),
            'compare_url' => (string) ($data['compare'] ?? ''),
            'pushed_at' => (string) ($data['head_commit']['timestamp'] ?? date('c')),
            'repository' => [
                'external_id' => (int) ($data['repository']['id'] ?? 0),
                'name' => (string) ($data['repository']['name'] ?? ''),
                'full_name' => (string) ($data['repository']['full_name'] ?? ''),
                'owner' => (string) ($data['repository']['owner']['login'] ?? ''),
                'html_url' => (string) ($data['repository']['html_url'] ?? ''),
                'default_branch' => (string) ($data['repository']['default_branch'] ?? ''),
            ],
            'pusher' => [
                'name' => (string) ($data['pusher']['name'] ?? ''),
                'email' => (string) ($data['pusher']['email'] ?? ''),
            ],
            'sender' => [
                'login' => (string) ($data['sender']['login'] ?? ''),
                'id' => (int) ($data['sender']['id'] ?? 0),
            ],
            'commits' => $this->extractCommits($data['commits'] ?? []),
            'raw_payload' => $data,
        ];
    }

    private function extractCommits(array $commits): array
    {
        $normalized = [];
        foreach ($commits as $commit) {
            if (!is_array($commit)) {
                continue;
            }

            $normalized[] = [
                'sha' => (string) ($commit['id'] ?? $commit['sha'] ?? ''),
                'message' => (string) ($commit['message'] ?? ''),
                'url' => (string) ($commit['url'] ?? ''),
                'added' => is_array($commit['added'] ?? null) ? $commit['added'] : [],
                'modified' => is_array($commit['modified'] ?? null) ? $commit['modified'] : [],
                'removed' => is_array($commit['removed'] ?? null) ? $commit['removed'] : [],
                'timestamp' => (string) ($commit['timestamp'] ?? date('c')),
                'author_name' => (string) ($commit['author']['name'] ?? ''),
                'author_email' => (string) ($commit['author']['email'] ?? ''),
                'distinct' => (bool) ($commit['distinct'] ?? false),
            ];
        }

        return $normalized;
    }

    private function getHeader(array $headers, string $name): ?string
    {
        if (isset($headers[$name])) {
            return (string) $headers[$name];
        }

        $lower = strtolower($name);
        foreach ($headers as $key => $value) {
            if (strtolower((string) $key) === $lower) {
                return (string) $value;
            }
        }

        return null;
    }
}
