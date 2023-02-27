<?php

declare(strict_types = 1);

use DI\ContainerBuilder;
use Psr\Log\LoggerInterface;

return static function (ContainerBuilder $containerBuilder, array $settings, LoggerInterface $logger) {
    $containerBuilder->addDefinitions([
        'settings' => $settings,
        LoggerInterface::class => $logger,
    ]);
};
