#!/usr/bin/env php
<?php

declare(strict_types = 1);

use App\Console\ReportCommand;
use App\Console\ImportFromGoogleSpreadSheetCommand;
use App\Settings;
use Symfony\Component\Console\Application;

/* @var \DI\Container  $container */
$container = require __DIR__ . '/config/bootstrap.php';

$cli = new Application('Translate console commands');

// Custom commands
$cli->addCommands([
    new ImportFromGoogleSpreadSheetCommand($container->get(Settings::class)),
    new ReportCommand(),
]);

$cli->run();
