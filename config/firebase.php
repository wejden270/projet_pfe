<?php

return [
    'default' => env('FIREBASE_PROJECT', 'app'),
    'projects' => [
        'app' => [
            'credentials' => storage_path('app/firebase/firebase-credentials.json'),
            'database' => [
                'url' => 'https://projet-ec820.firebaseio.com',
            ],
        ],
    ],
];
