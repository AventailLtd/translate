#!/usr/bin/env php
<?php

declare(strict_types = 1);

use App\Console\ImportFromGoogleSpreadSheetCommand;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/config/bootstrap.php';

$cli = new Application('Translate console commands');

// Custom commands
$cli->addCommands([
    new ImportFromGoogleSpreadSheetCommand(),
]);

$cli->run();
