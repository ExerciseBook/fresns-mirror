<?php

use App\Fresns\Panel\Http\Middleware\ChangeLocale;

return [
    'news_url' => 'https://fresns.cn/news.json',
    'middleware' => [
        'web',
        ChangeLocale::class,
    ],
    'langs' => [
        'en' => 'English - English',
        'es' => 'Español - Spanish',
        'fr' => 'Français - French',
        'de' => 'Deutsch - German',
        'ja' => '日本語 - Japanese',
        'ko' => '한국어 - Korean',
        'ru' => 'Русский - Russian',
        'pt' => 'Português - Portuguese',
        'id' => 'Bahasa Indonesia - Indonesian',
        'hi' => 'हिन्दी - Hindi',
        'zh-Hans' => '简体中文 - Chinese (Simplified)',
        'zh-Hant' => '繁體中文 - Chinese (Traditional)',
    ]
];
