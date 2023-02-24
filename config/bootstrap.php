<?php

declare(strict_types = 1);

use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

require __DIR__ . '/../vendor/autoload.php';

$settings = (require __DIR__ . '/settings.php')(
    $_ENV['APP_ENV'] ?? 'dev'
);

// Create PSR-3 logger:
$logger = new Logger($settings['logger']['name']);
$processor = new UidProcessor();
$logger->pushProcessor($processor);
$handler = new StreamHandler($settings['logger']['path'], $settings['logger']['level']);
$logger->pushHandler($handler);

// Error handling:
error_reporting(E_ALL);
$errorHandler = new ErrorHandler($logger);
// This is the basic error handler, so there is no need to call the previous error handler.
$errorHandler->registerErrorHandler([], false);
$errorHandler->registerExceptionHandler([], false);
$errorHandler->registerFatalHandler();
