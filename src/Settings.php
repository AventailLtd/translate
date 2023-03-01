<?php

declare(strict_types = 1);

namespace App;

use Psr\Log\LogLevel;

class Settings
{
    private string $appEnv;
    private array $logger;
    private string|null $diCompilationPath;
    private string $exportDir;
    private array $google;

    public function __construct(string $appEnv)
    {
        $this->appEnv = $appEnv;
        $this->diCompilationPath = __DIR__ . '/../var/cache/di_compilation';
        $this->exportDir = __DIR__ . '/../var/export';
        $this->logger = [
            'name' => 'general',
            'path' => $_ENV['LOGGER_PATH'] ?? 'php://stdout',
            'level' => $_ENV['LOGGER_LEVEL'] ?? LogLevel::DEBUG,
        ];

        if ($this->appEnv === 'dev') {
            // Overrides for development mode
            $this->diCompilationPath = null;
        }
        $this->google = [
            'credentials_file' => $_ENV['GOOGLE_CREDENTIALS_FILENAME'] ? __DIR__ . '/../var/keys/' . $_ENV['GOOGLE_CREDENTIALS_FILENAME'] : null,
        ];
    }

    public function getAppEnv(): string
    {
        return $this->appEnv;
    }

    public function getLogger(): array
    {
        return $this->logger;
    }

    public function getDiCompilationPath(): ?string
    {
        return $this->diCompilationPath;
    }

    public function getExportDir(): string
    {
        return $this->exportDir;
    }

    public function getGoogle(): array
    {
        return $this->google;
    }
}
