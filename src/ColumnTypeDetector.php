<?php

namespace Konekt\LaravelMigrationCompatibility;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Konekt\LaravelMigrationCompatibility\Factories\DatabaseDetector;
use PDO;

class ColumnTypeDetector
{
    private const CONFIG_ROOT = 'migration.compatibility.map';

    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function getType(PDO $pdo, string $table, string $column): IntegerField
    {
        return (new self($pdo))->getColumnType($table, $column);
    }

    public function getColumnType(string $table, string $column): IntegerField
    {
        // Attempt to obtain from database
        $dbDetector = DatabaseDetector::fromPdoDriver($this->pdo);
        if ($dbDetector) {
            $result = $dbDetector->run($table, $column);
            if (!$result->isUnknown()) {
                return $result;
            }
        }

        // Attempt to obtain from package configuration
        $result = $this->detectFromConfiguration($table, $column);
        if (!$result->isUnknown()) {
            return $result;
        }

        // Worst case scenario, make a guess based on Laravel version
        return $this->fallbackToGuessFromLaravelVersionNumber($table, $column);
    }

    private function detectFromConfiguration(string $table, string $column): IntegerField
    {
        $config = config(static::CONFIG_ROOT . ".$table.$column");

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

    private function fallbackToGuessFromLaravelVersionNumber(string $table, string $column): IntegerField
    {
        // Trigger a notice to help folks finding the right path when shit hits the fan
        trigger_error(
            sprintf("Could not detect type for the `%s.%s` relation.\nSet the %s config entry to either `%s` or `%s`.",
                $table, $column, static::CONFIG_ROOT . ".$table.$column", 'int [unsigned]', 'bigint [unsigned]'
            ), E_USER_NOTICE
        );

        if (version_compare(App::version(), '5.8.0', '>=')) {
            return IntegerField::BIGINT()->unsigned();
        }

        return IntegerField::INTEGER()->unsigned();
    }
}
