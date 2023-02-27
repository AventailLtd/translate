<?php

declare(strict_types = 1);

namespace App\Console;

use Google\Service\Sheets;
use Google_Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportFromGoogleSpreadSheetCommand extends Command
{
    protected static $defaultName = 'import:google-spreadsheet';
    protected static $defaultDescription = 'Import translate keys from Google Spreadsheets';

    public function __construct(protected array $settings, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument('spreadsheet-id', InputArgument::REQUIRED, 'Spreadsheet ID');
        $this->addArgument('range', InputArgument::REQUIRED, 'Spreadsheet range e.g.: "A3:E"');
        $this->addArgument('lang-list', InputArgument::REQUIRED, 'Language list in order, e.g.: "hu,en,de,ro,ru". Must be consistent with the specified column range.');
        $this->addOption(
            'separate',
            's',
            InputOption::VALUE_NONE,
            'Separate mode: separate translate files by prefix.',
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $spreadsheetId = $input->getArgument('spreadsheet-id');
        $range = $input->getArgument('range');
        $langList = $input->getArgument('lang-list');
        $isSeparateMode = $input->getOption('separate');

        $credentialsFile = $this->settings['google']['credentials_file'];

        if (!file_exists($credentialsFile)) {
            throw new \LogicException('Credentials file not found: ' . $credentialsFile);
        }

        $client = new Google_Client();
        $client->setApplicationName('Google Docs API PHP');
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig($credentialsFile);

        $service = new Sheets($client);

        $output->write('Download Google spreadsheet ... ');
        $rows = $service->spreadsheets_values->get($spreadsheetId, $range)['values'];
        $output->writeln('<info>OK</info>');

        $translatesByLangId = [];
        foreach ($rows as $row) {
            // The language key must always be the first column.
            $languageKey = $row[0];

            foreach (explode(',', $langList) as $columnIndex => $langId) {
                // Column index + 1, because the first column is the language key.
                $translatesByLangId[$langId][$languageKey] = $row[$columnIndex + 1] ?? null;
            }
        }

        $exportDir = $this->settings['export_dir'];

        // Create dir if not exists.
        if (!is_dir($exportDir)) {
            mkdir($exportDir, 0777, true);
        }

        if (!$isSeparateMode) {
            foreach ($translatesByLangId as $langId => $translates) {
                $output->write('Export "' . $langId . '" ... ');

                $exportPath = $exportDir . '/' . $langId . '.json';
                file_put_contents($exportPath, json_encode($translates, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $output->writeln('<info>OK</info>');
            }
        } else {
            $separateTranslates = [];

            foreach ($translatesByLangId as $langId => $translates) {
                foreach ($translates as $languageKey => $translate) {
                    // The first key prefix will be the name of the file.
                    $separateTranslates[explode('.', $languageKey)[0]][$langId][$languageKey] = $translate;
                }
            }

            foreach ($separateTranslates as $separateKey => $explodedTranslate) {
                $output->write('Export "' . $separateKey . '" ... ');

                $exportPath = $exportDir . '/' . $separateKey . '.json';

                file_put_contents($exportPath, json_encode($explodedTranslate, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $output->writeln('<info>OK</info>');
            }
        }

        return Command::SUCCESS;
    }
}
