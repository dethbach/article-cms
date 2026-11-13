<?php

return [
    'name'        => env('APP_NAME', 'Article CMS'),
    'tagline'     => env('SITE_TAGLINE', 'Stories, guides and ideas worth reading'),
    'description' => env('SITE_DESCRIPTION', 'Read our latest articles and browse by category.'),
    'icon'        => 'bi-journal-richtext',   // any Bootstrap Icon
    'login_url'   => '/login',
    'home_url'    => '/article',

    // leave a value empty to hide that icon in the footer
    'social' => [
        'facebook'  => '',
        'instagram' => '',
        'x'         => '',
        'youtube'   => '',
    ],

    'hero' => [
        'eyebrow'   => env('SITE_HERO_EYEBROW', 'Insights, Stories & Articles'),
        'title'     => env('SITE_HERO_TITLE', 'Discover stories, guides and'),
        'highlight' => env('SITE_HERO_HIGHLIGHT', 'ideas worth reading'),   // shown with the gradient
        'subtitle'  => env('SITE_HERO_SUBTITLE', 'Search our articles or jump straight into a topic you care about.'),
        'placeholder' => 'Search articles...',
    ],

    'seo' => [
        // paths crawlers should skip (add your role prefixes)
        'disallow' => ['/admin', '/login', '/*?q='],

        // let AI search and assistant crawlers read the site (better GEO visibility)
        'allow_ai' => env('SITE_ALLOW_AI', true),
        'ai_bots'  => [
            'GPTBot', 'OAI-SearchBot', 'ChatGPT-User',
            'ClaudeBot', 'Claude-SearchBot', 'Claude-User',
            'PerplexityBot', 'Perplexity-User',
            'Google-Extended', 'Applebot-Extended',
        ],
    ],
];
