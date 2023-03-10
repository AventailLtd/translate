<?php

declare(strict_types = 1);

namespace App\Report;

use Symfony\Component\Finder\Finder;

class UsedKeys
{
    public const TYPE_VUE = 'vue';
    public const TYPE_PHP = 'php';
    public const TYPE_PHP_RAIN_TPL = 'rain_tpl';

    public const RULES = [
        self::TYPE_VUE => [
            '/\\$t\\(\'(?<key>[^\']{0,255})\'\\)/',
            '/v-t="\'(?<key>[^"]{0,255})\'"/',
        ],
        self::TYPE_PHP => [
            '/lang\\(\'(?<key>[^\']{0,255})\'(,\s*[^\\)]+)?\\)/',
        ],
        self::TYPE_PHP_RAIN_TPL => [
            '/\\{\'(?<key>[^\']+)\'\\|lang(:.+)?\\}/',
        ],
    ];

    public const TYPE_PATTERNS = [
        self::TYPE_VUE => ['*.vue', '*.js'],
        self::TYPE_PHP => ['*.php'],
        self::TYPE_PHP_RAIN_TPL => ['*.html'],
    ];

    /**
     * Collect used keys of path
     *
     * @param string $path
     * @return array
     */
    public static function scan(string $path): array
    {
        $finder = new Finder();

        $usedKeys = [];
        foreach (static::TYPE_PATTERNS as $type => $pattern) {
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
