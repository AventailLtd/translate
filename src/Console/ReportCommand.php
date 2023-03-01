<?php

declare(strict_types = 1);

namespace App\Console;

use App\Report\UsedKeys;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ReportCommand extends Command
{
    protected static $defaultName = 'report';
    protected static $defaultDescription = 'Check missing, unused translates';

    protected const ARGUMENT_LANGUAGE_FILES_DIR = 'language-files-dir';
    protected const ARGUMENT_SCAN_DIRS = 'scan-dirs';
    protected const ARGUMENT_IGNORE_KEYS_PATH = 'ignore-keys-path';
    protected const OPTION_STRICT = 'strict';

    protected function configure(): void
    {
        $this->addArgument(static::ARGUMENT_LANGUAGE_FILES_DIR, InputArgument::REQUIRED, 'Lanaguage files directory');
        $this->addArgument(static::ARGUMENT_SCAN_DIRS, InputArgument::REQUIRED, 'Comma separated directories to scan missing/unused language keys.');
        $this->addArgument(static::ARGUMENT_IGNORE_KEYS_PATH, InputArgument::OPTIONAL, 'JSON file which includes ignored language keys.');
        $this->addOption(
            static::OPTION_STRICT,
            's',
            InputOption::VALUE_NONE,
            'In strict mode, unused keys are also considered errors. (exit 1)',
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new Finder();

        // Collect translate files (the name of the json file means the translation language.)
        $translatesByLangId = [];
        foreach ($finder->files()->name('*.json')->in($input->getArgument(static::ARGUMENT_LANGUAGE_FILES_DIR)) as $file) {
            if ($output->isVerbose()) {
                $output->writeln('Founded language file: ' . $file->getRealPath());
            }

            $langId = $file->getFilenameWithoutExtension();
            $content = json_decode($file->getContents(), true, 512, JSON_THROW_ON_ERROR);

            if (!array_key_exists($langId, $translatesByLangId)) {
                $translatesByLangId[$langId] = $content;
            } else {
                $translatesByLangId[$langId] = array_merge($translatesByLangId[$langId], $content);
            }
        }

        // Scan files to collect all used language keys.
        $allLanguageKeys = [];
        foreach (explode(',', $input->getArgument(static::ARGUMENT_SCAN_DIRS)) as $path) {
            if ($output->isVerbose()) {
                $output->writeln('Scan language keys in: ' . realpath($path));
            }
            $allLanguageKeys = array_unique(array_merge($allLanguageKeys, UsedKeys::scan($path)));
        }

        $ignoreKeys = [];
        if ($input->getArgument(static::ARGUMENT_IGNORE_KEYS_PATH) !== null) {
            $ignoreKeys = json_decode(file_get_contents($input->getArgument(static::ARGUMENT_IGNORE_KEYS_PATH)), true, 512, JSON_THROW_ON_ERROR);
        }

        // If error is true then the return value of the script will be 1. Useful for CI/CD.
        $error = false;

        // Check missing keys
        foreach ($allLanguageKeys as $languageKey) {
            $missingFromLanguages = [];
            foreach ($translatesByLangId as $langId => $translates) {
                if (in_array($languageKey, $ignoreKeys)) {
                    continue;
                }

                if (!array_key_exists($languageKey, $translates) || $translates[$languageKey] === '') {
                    // Translate not exists or empty.
                    $error = true;
                    $missingFromLanguages[] = $langId;
                }
            }

            if (count($missingFromLanguages) > 0) {
                $output->writeln('<error>Missing key: ' . $languageKey . '" from: ' . implode(',', $missingFromLanguages) . '</error>');
            }
        }

        // Check unused keys
        $unusedKeys = [];
        foreach ($translatesByLangId as $translates) {
            foreach ($translates as $languageKey => $translate) {
                if (in_array($languageKey, $ignoreKeys)) {
                    continue;
                }

                if (!in_array($languageKey, $allLanguageKeys) && !in_array($languageKey, $unusedKeys)) {
                    $unusedKeys[] = $languageKey;
                }
            }
        }
        foreach ($unusedKeys as $unusedKey) {
            if ($input->getOption(static::OPTION_STRICT)) {
                $output->writeln('<error>Unused key: ' . $unusedKey . '</error>');
                $error = true;
            } else {
                $output->writeln('<fg=yellow>Unused key: ' . $unusedKey . '</>');
            }
        }

        if ($error) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
