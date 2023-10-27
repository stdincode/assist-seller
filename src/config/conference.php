<?php

return [
    // разница во времени в секундах до начала конференции для создания ссылки и оповещения
    'before_start_conference_diff_seconds' => 70 * 60,

    'hosting' => [
        'jitsi' => [
            'jitsi_app_host' => env('JITSI_APP_HOST'),
            'jitsi_app_id' => env('JITSI_APP_ID'),
            'jitsi_app_secret' => env('JITSI_APP_SECRET'),
        ],
    ],

];
