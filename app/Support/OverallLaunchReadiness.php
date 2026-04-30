<?php

namespace App\Support;

use App\Models\RoadmapItem;

class OverallLaunchReadiness
{
    public static function report(): array
    {
        $beta = BetaReadiness::report();
        $goLive = GoLiveReadiness::report();
        $githubProgram = GitHubProgramReadiness::report();
        $releaseEvidence = ReleaseEvidenceReadiness::report();
        $roadmapTotal = RoadmapItem::count();
        $roadmapDone = RoadmapItem::where('status', 'done')->count();
        $roadmapPercent = $roadmapTotal > 0 ? (int) round(($roadmapDone / $roadmapTotal) * 100) : 0;

        $areas = [
            [
                'title' => 'Beta/local',
                'percent' => $beta['percent'],
                'detail' => 'Produto funcional em ambiente controlado.',
                'status' => $beta['ready'] ? 'ok' : 'pendente',
            ],
            [
                'title' => 'Go-live',
                'percent' => $goLive['percent'],
                'detail' => 'Critérios para operação pública e produção.',
                'status' => $goLive['ready'] ? 'ok' : 'bloqueado',
            ],
            [
                'title' => 'GitHub Program',
                'percent' => $githubProgram['percent'],
                'detail' => 'Narrativa, evidências e requisitos para submissão.',
                'status' => $githubProgram['summary']['missing'] === 0 ? 'ok' : 'pendente',
            ],
            [
                'title' => 'Evidências',
                'percent' => $releaseEvidence['percent'],
                'detail' => $releaseEvidence['done'].'/'.$releaseEvidence['total'].' materiais de release prontos.',
                'status' => $releaseEvidence['ready'] ? 'ok' : 'pendente',
            ],
            [
                'title' => 'Roadmap',
                'percent' => $roadmapPercent,
                'detail' => $roadmapDone.'/'.$roadmapTotal.' itens concluídos.',
                'status' => $roadmapPercent >= 80 ? 'ok' : 'pendente',
            ],
        ];

        $overall = (int) round(collect($areas)->avg('percent'));

        return [
            'percent' => $overall,
            'areas' => $areas,
            'go_live_blockers' => $goLive['blockers'],
            'release_evidence' => $releaseEvidence,
            'roadmap' => [
                'done' => $roadmapDone,
                'total' => $roadmapTotal,
                'percent' => $roadmapPercent,
            ],
            'ready_for_public_launch' => $goLive['ready'] && $overall >= 90,
        ];
    }
}