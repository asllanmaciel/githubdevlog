<?php

namespace App\Support;

class ReleaseEvidenceReadiness
{
    public static function report(): array
    {
        $items = collect([
            self::item('Proposta pública', 'Landing page explica dor, público, solução e caminho de uso.', file_exists(resource_path('views/landing.blade.php')), '/'),
            self::item('Documentação para usuários', 'Guia de uso com configuração, endpoint, secret, eventos e troubleshooting.', file_exists(resource_path('views/docs/users.blade.php')) || file_exists(base_path('docs/users.md')), '/docs/usuarios'),
            self::item('Referência de API', 'Rotas públicas, webhooks, respostas e códigos de erro documentados.', file_exists(resource_path('views/docs/api.blade.php')), '/docs/api'),
            self::item('Páginas legais', 'Privacidade, termos e segurança publicados para avaliação externa.', file_exists(resource_path('views/legal/privacy.blade.php')) && file_exists(resource_path('views/legal/terms.blade.php')) && file_exists(resource_path('views/legal/security.blade.php')), '/privacy, /terms, /security'),
            self::item('Contato e suporte', 'Canal público de contato e suporte autenticado para usuários.', file_exists(resource_path('views/contact.blade.php')) && file_exists(resource_path('views/support.blade.php')), '/contact, /support'),
            self::item('Checklist GitHub Developer Program', 'Narrativa, requisitos, permissões e pontos de submissão organizados.', file_exists(base_path('docs/github-developer-program.md')) || file_exists(base_path('docs/github-program-readiness.md')), '/admin/github-program'),
            self::item('Checklist go-live', 'Bloqueadores de produção separados de itens locais já entregues.', file_exists(base_path('docs/production-launch-checklist.md')), '/admin/go-live'),
            self::item('Checklist técnico de produção', 'Variáveis, integrações e ambiente final rastreados no admin.', file_exists(base_path('docs/production-environment-readiness.md')), '/admin/production-env'),
            self::item('Changelog público', 'Histórico de evolução visível para gerar confiança em devs e avaliadores.', file_exists(resource_path('views/changelog.blade.php')), '/changelog'),
            self::item('Sitemap e robots', 'Descoberta pública básica pronta para indexação e revisão.', file_exists(resource_path('views/sitemap.blade.php')), '/sitemap.xml, /robots.txt'),
        ]);

        $done = $items->where('done', true)->count();
        $total = max($items->count(), 1);

        return [
            'percent' => (int) round(($done / $total) * 100),
            'done' => $done,
            'total' => $total,
            'ready' => $done === $total,
            'items' => $items,
            'missing' => $items->where('done', false)->values(),
        ];
    }

    private static function item(string $title, string $description, bool $done, string $where): array
    {
        return [
            'title' => $title,
            'description' => $description,
            'done' => $done,
            'where' => $where,
        ];
    }
}
