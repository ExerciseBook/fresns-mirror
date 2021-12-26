<?php

use App\Fresns\Panel\Http\Middleware\ChangeLocale;

return [
    'middleware' => [
        'web',
        ChangeLocale::class,
    ]
];
