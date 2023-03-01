<?php

declare(strict_types = 1);

use App\Settings;
use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;

return static function (ContainerBuilder $containerBuilder, Settings $settings, LoggerInterface $logger) {
    $containerBuilder->addDefinitions([
        Settings::class => $settings,
        LoggerInterface::class => $logger,
    ]);
};
