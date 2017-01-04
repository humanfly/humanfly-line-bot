<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'bot' => [
            'channelToken' => getenv('LINEBOT_CHANNEL_TOKEN') ?: '<your channel token>',
            'channelSecret' => getenv('LINEBOT_CHANNEL_SECRET') ?: '<your channel secret>',
        ],
        'apiEndpointBase' => getenv('LINEBOT_API_ENDPOINT_BASE'),
    ],
];
