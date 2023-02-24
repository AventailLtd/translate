<?php

declare(strict_types = 1);

use Psr\Log\LogLevel;

return static function (string $appEnv) {
    $settings = [
        'env' => $appEnv,
        'logger' => [
            'name' => 'general',
            'path' => $_ENV['LOGGER_PATH'] ?? 'php://stdout',
            'level' => $_ENV['LOGGER_LEVEL'] ?? LogLevel::DEBUG,
        ],
        'google' => [
            'credentials_file' => $_ENV['GOOGLE_CREDENTIALS_FILENAME'] ?  __DIR__ . '/../var/keys/' . $_ENV['GOOGLE_CREDENTIALS_FILENAME'] : null,
        ],
    ];

    return $settings;
};
