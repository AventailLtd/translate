<?php

declare(strict_types = 1);

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFromGoogleSpreadSheetCommand extends Command
{
    protected static $defaultName = 'import:google-spreadsheet';
    protected static $defaultDescription = 'Import translate keys from Google Spreadsheets';

    protected function configure(): void
    {
        $this->addArgument('spreadsheet-id', InputArgument::REQUIRED, 'Spreadsheet ID');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $spreadsheetId = $input->getArgument('spreadsheet-id');
        // TODO: implement

        return Command::SUCCESS;
    }
}
