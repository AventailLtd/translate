<?php

declare(strict_types = 1);

use App\Settings;
use DI\ContainerBuilder;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

require __DIR__ . '/../vendor/autoload.php';

$settings = new Settings($_ENV['APP_ENV'] ?? 'dev');

// Create PSR-3 logger:
$logger = new Logger($settings->getLogger()['name']);
$processor = new UidProcessor();
$logger->pushProcessor($processor);
$handler = new StreamHandler($settings->getLogger()['path'], $settings->getLogger()['level']);
$logger->pushHandler($handler);

// Error handling:
error_reporting(E_ALL);
$errorHandler = new ErrorHandler($logger);
// This is the basic error handler, so there is no need to call the previous error handler.
$errorHandler->registerErrorHandler([], false);
$errorHandler->registerExceptionHandler([], false);
$errorHandler->registerFatalHandler();

// Container building and dependency injection:
$containerBuilder = new ContainerBuilder();
if ($settings->getDiCompilationPath() !== null) {
    $containerBuilder->enableCompilation($settings->getDiCompilationPath());
}

(require __DIR__ . '/dependencies.php')($containerBuilder, $settings, $logger);

return $containerBuilder->build();
