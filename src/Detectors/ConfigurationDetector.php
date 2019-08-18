<?php
/**
 * Contains the ConfigurationDetector class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-08-18
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Detectors;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Str;
use Konekt\LaravelMigrationCompatibility\Contracts\FieldTypeDetector;
use Konekt\LaravelMigrationCompatibility\IntegerField;
use PDO;

class ConfigurationDetector implements FieldTypeDetector
{
    public const CONFIG_ROOT = 'migration.compatibility.map';

    /** @var Repository */
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function run(string $table, string $column): IntegerField
    {
        $config = $this->config->get(static::CONFIG_ROOT . " . $table . $column");

        if (null === $config) {
            return IntegerField::UNKNOWN();
        }

        $config = strtolower($config);

        if (Str::contains($config, 'bigint')) {
            $result = IntegerField::BIGINT();
        } elseif (Str::contains($config, 'int')) {
            $result = IntegerField::INTEGER();
        } else {
            return IntegerField::UNKNOWN();
        }

        return $result->unsigned(Str::contains($config, 'unsigned'));
    }

    public function wantsPdo(): bool
    {
        return false;
    }

    public function setPdo(PDO $pdo): void
    {
    }
}
