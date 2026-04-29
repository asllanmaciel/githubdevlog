<?php

namespace App\Support;

use App\Models\LaunchTest;

class SubmissionAssets
{
    public const AREA = 'Submission';

    public static function required(): array
    {
        return [
            ['title' => 'Screenshot - Landing publica', 'priority' => 'high', 'instructions' => 'Capturar a home mostrando proposta de valor, publico alvo e CTA principal.', 'expected_result' => 'Imagem mostra claramente que o produto e uma ferramenta para devs que usam webhooks do GitHub.'],
            ['title' => 'Screenshot - Dashboard do usuario', 'priority' => 'high', 'instructions' => 'Capturar dashboard com workspace, endpoint, secret mascarado e cards de visao geral.', 'expected_result' => 'Imagem prova que o dev entende como conectar e acompanhar o workspace.'],
            ['title' => 'Screenshot - Evento GitHub validado', 'priority' => 'high', 'instructions' => 'Capturar evento real ou demo com delivery id, repositorio, assinatura valida e payload organizado.', 'expected_result' => 'Imagem prova o valor principal: validar e entender webhooks GitHub.'],
            ['title' => 'Screenshot - Notas e tarefas no webhook', 'priority' => 'medium', 'instructions' => 'Capturar um evento com nota ou tarefa associada.', 'expected_result' => 'Imagem diferencia o produto de um log bruto ou request bin generico.'],
            ['title' => 'Screenshot - Gate de lancamento', 'priority' => 'high', 'instructions' => 'Capturar /admin/launch-gate com score, bloqueadores e comando strict.', 'expected_result' => 'Imagem mostra maturidade operacional para release.'],
            ['title' => 'Screenshot - Security Center', 'priority' => 'high', 'instructions' => 'Capturar checks de seguranca, webhooks assinados e postura geral.', 'expected_result' => 'Imagem reforca seguranca, privacidade e governanca.'],
            ['title' => 'Screenshot - Billing e planos', 'priority' => 'medium', 'instructions' => 'Capturar tela de planos ou assinaturas no admin.', 'expected_result' => 'Imagem mostra modelo SaaS e monetizacao por uso.'],
            ['title' => 'Video curto - Fluxo GitHub webhook', 'priority' => 'high', 'instructions' => 'Gravar ate 90 segundos: configurar webhook no GitHub, enviar ping/push e visualizar evento validado.', 'expected_result' => 'Video demonstra fluxo ponta a ponta com valor claro para devs.'],
        ];
    }

    public static function ensureSeeded(): void
    {
        foreach (self::required() as $index => $asset) {
            LaunchTest::updateOrCreate(
                ['title' => $asset['title'], 'area' => self::AREA],
                [
                    'priority' => $asset['priority'],
                    'status' => 'pending',
                    'instructions' => $asset['instructions'],
                    'expected_result' => $asset['expected_result'],
                    'position' => $index + 1,
                ]
            );
        }
    }

    public static function report(): array
    {
        $items = LaunchTest::where('area', self::AREA)->orderBy('position')->orderBy('id')->get();
        $total = max($items->count(), 1);
        $done = $items->where('status', 'passed')->count();
        $withEvidence = $items->filter(fn ($item) => filled($item->evidence))->count();

        return [
            'items' => $items,
            'total' => $items->count(),
            'done' => $done,
            'with_evidence' => $withEvidence,
            'percent' => round(($done / $total) * 100),
            'evidence_percent' => round(($withEvidence / $total) * 100),
            'ready' => $items->count() > 0 && $done === $items->count() && $withEvidence === $items->count(),
        ];
    }
}