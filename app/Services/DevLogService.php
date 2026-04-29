<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

final class DevLogService
{
    private readonly OpenAIService $openAiService;

    public function __construct(?OpenAIService $openAiService = null)
    {
        $this->openAiService = $openAiService ?? new OpenAIService();
    }

    public function processPushEvent(array $payload, string $deliveryId): array
    {
        $pdo = Database::getConnection();

        $repository = $payload['repository'];
        $repoId = $this->getOrCreateRepository($pdo, $repository);

        $existing = $this->findEventByDeliveryId($pdo, $deliveryId);
        if ($existing !== null) {
            return [
                'status' => 'already_processed',
                'event_id' => $deliveryId,
                'devlog_id' => (int) $existing['devlog_id'],
                'created_at' => (string) $existing['created_at'],
            ];
        }

        $pushEventId = $this->createPushEvent($pdo, $repoId, $payload, $deliveryId);
        $this->insertCommits($pdo, $pushEventId, $payload['commits'] ?? []);

        $summary = $this->openAiService->generateDevLog(
            $repository,
            $payload['commits'] ?? [],
            $payload['before'],
            $payload['after'],
            $payload['pusher']['name'] ?? ''
        );

        $devlogId = $this->createDevlog($pdo, $pushEventId, $summary, $payload);

        return [
            'status' => 'processed',
            'event_id' => $deliveryId,
            'repository' => [
                'id' => $repoId,
                'full_name' => $repository['full_name'],
            ],
            'push_event_id' => $pushEventId,
            'devlog_id' => $devlogId,
            'summary' => $summary,
            'commits_count' => count($payload['commits'] ?? []),
        ];
    }

    private function findEventByDeliveryId(PDO $pdo, string $deliveryId): ?array
    {
        $stmt = $pdo->prepare('SELECT id, created_at, (SELECT id FROM devlogs WHERE push_event_id = push_events.id LIMIT 1) AS devlog_id FROM push_events WHERE event_id = ? LIMIT 1');
        $stmt->execute([$deliveryId]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }
        return [
            'id' => (int) $row['id'],
            'created_at' => (string) $row['created_at'],
            'devlog_id' => (int) ($row['devlog_id'] ?? 0),
        ];
    }

    private function getOrCreateRepository(PDO $pdo, array $repository): int
    {
        $select = $pdo->prepare('SELECT id FROM repositories WHERE external_id = ? LIMIT 1');
        $select->execute([(int) $repository['external_id']]);
        $existing = $select->fetch();
        if ($existing !== false) {
            return (int) $existing['id'];
        }

        $insert = $pdo->prepare('INSERT INTO repositories
            (external_id, name, full_name, owner, html_url, default_branch)
            VALUES (?, ?, ?, ?, ?, ?)');
        $insert->execute([
            (int) $repository['external_id'],
            $repository['name'],
            $repository['full_name'],
            $repository['owner'],
            $repository['html_url'],
            $repository['default_branch'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    private function createPushEvent(PDO $pdo, int $repositoryId, array $payload, string $deliveryId): int
    {
        $insert = $pdo->prepare('INSERT INTO push_events
            (repository_id, event_id, ref, before_sha, after_sha, compare_url, pushed_at, pusher_name, pusher_email, raw_payload)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $insert->execute([
            $repositoryId,
            $deliveryId ?: $this->generateEventIdFallback(),
            $payload['ref'],
            $payload['before'],
            $payload['after'],
            $payload['compare_url'],
            $payload['pushed_at'],
            $payload['pusher']['name'] ?? '',
            $payload['pusher']['email'] ?? '',
            json_encode($payload['raw_payload'], JSON_UNESCAPED_UNICODE),
        ]);

        return (int) $pdo->lastInsertId();
    }

    private function insertCommits(PDO $pdo, int $pushEventId, array $commits): void
    {
        $insert = $pdo->prepare('INSERT INTO commits
            (push_event_id, sha, message, author_name, author_email, committed_at, url, added_files, modified_files, removed_files, is_distinct)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

        foreach ($commits as $commit) {
            if (!is_array($commit)) {
                continue;
            }

            $insert->execute([
                $pushEventId,
                $commit['sha'],
                $commit['message'],
                $commit['author_name'],
                $commit['author_email'],
                $commit['timestamp'],
                $commit['url'],
                json_encode($commit['added'], JSON_UNESCAPED_UNICODE),
                json_encode($commit['modified'], JSON_UNESCAPED_UNICODE),
                json_encode($commit['removed'], JSON_UNESCAPED_UNICODE),
                $commit['distinct'] ? 1 : 0,
            ]);
        }
    }

    private function createDevlog(PDO $pdo, int $pushEventId, array $summary, array $payload): int
    {
        $statement = $pdo->prepare('INSERT INTO devlogs
            (push_event_id, generated_by, model, summary_text, raw_response, usage_info, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)');
        $statement->execute([
            $pushEventId,
            $summary['provider'],
            $summary['model'],
            $summary['summary'],
            json_encode($summary, JSON_UNESCAPED_UNICODE),
            json_encode($summary['usage'], JSON_UNESCAPED_UNICODE),
            $payload['pushed_at'],
        ]);

        return (int) $pdo->lastInsertId();
    }

    private function generateEventIdFallback(): string
    {
        return bin2hex(random_bytes(12));
    }
}
