<?php

declare(strict_types = 1);

namespace App\Report;

use Symfony\Component\Finder\Finder;

class UsedKeys
{
    public const string TYPE_VUE = 'vue';
    public const string TYPE_PHP = 'php';
    public const string TYPE_PHP_RAIN_TPL = 'rain_tpl';

    public const array RULES = [
        self::TYPE_VUE => [
            // $t('test') OR $t('test2', { obj1: 123 })
            '/\$t\(\s*\'(?<key>[^\']{0,255})\'\s*(?:,\s*\{[\s\S]*?\})?\s*\)/',
            // t('test') OR t('test2', { obj1: 123 }) - but not $t() or something_t()
            '/(?<![\w$])t\(\s*\'(?<key>[^\']{0,255})\'\s*(?:,\s*\{[\s\S]*?\})?\s*\)/',
            // v-t="test"
            '/v-t="\'(?<key>[^"]{0,255})\'"/',
        ],
        self::TYPE_PHP => [
            '/lang\\(\'(?<key>[^\']{0,255})\'(,\s*[^\\)]+)?\\)/',
            // laravel translate method.
            '/__\\(\'(?<key>[^\']{0,255})\'/',
            // custom translate method.
            '/translateWithMarkers\\(\'(?<key>[^\']{0,255})\'/',
        ],
        self::TYPE_PHP_RAIN_TPL => [
            '/\\{\'(?<key>[^\']+)\'\\|lang(:.+)?\\}/',
        ],
    ];

    public const array FILETYPE_PATTERNS = [
        self::TYPE_VUE => ['*.vue', '*.js', '*.ts'],
        self::TYPE_PHP => ['*.php'],
        self::TYPE_PHP_RAIN_TPL => ['*.html'],
    ];

    /**
     * Collect used keys of path
     *
     * @param string $path
     * @return string[]
     */
    public static function scan(string $path): array
    {
        $finder = new Finder();

        $usedKeys = [];
        foreach (static::FILETYPE_PATTERNS as $type => $pattern) {
            foreach ($finder->files()->name($pattern)->in($path) as $file) {
                foreach (static::RULES[$type] as $rule) {
                    if (preg_match_all($rule, $file->getContents(), $matches)) {
                        $usedKeys = array_unique(array_merge($usedKeys, $matches['key']));
                    }
                }
            }
        }

        return $usedKeys;
    }
}
