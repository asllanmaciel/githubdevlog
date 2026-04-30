<?php

return [
    'creator_name' => env('DEVLOG_CREATOR_NAME', 'Asllan Maciel'),
    'creator_role' => env('DEVLOG_CREATOR_ROLE', 'Criador e mantenedor do GitHub DevLog AI'),
    'creator_url' => env('DEVLOG_CREATOR_URL', 'https://github.com/asllanmaciel'),
    'creator_avatar_url' => env('DEVLOG_CREATOR_AVATAR_URL', 'https://avatars.githubusercontent.com/u/397983?s=400&u=45e16a1190f56a1840bffda73f18a463f154ae31&v=4'),
    'company_name' => env('DEVLOG_COMPANY_NAME', 'GitHub DevLog AI'),
    'support_email' => env('DEVLOG_SUPPORT_EMAIL', 'suporte@githubdevlog.ai'),
    'analytics' => [
        'google_tag_manager_id' => env('DEVLOG_GTM_ID'),
        'google_analytics_id' => env('DEVLOG_GA_MEASUREMENT_ID'),
        'meta_pixel_id' => env('DEVLOG_META_PIXEL_ID'),
        'hotjar_id' => env('DEVLOG_HOTJAR_ID'),
        'clarity_id' => env('DEVLOG_CLARITY_ID'),
        'plausible_domain' => env('DEVLOG_PLAUSIBLE_DOMAIN'),
    ],
];

