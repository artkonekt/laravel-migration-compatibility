<?php

namespace Konekt\LaravelMigrationCompatibility;

use Illuminate\Support\Facades\App;
use Konekt\LaravelMigrationCompatibility\Detectors\ConfigurationDetector;
use Konekt\LaravelMigrationCompatibility\Detectors\FromLaravelVersionDetector;
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
        $result = (new ConfigurationDetector(App::make('config')))->run($table, $column);
        if (!$result->isUnknown()) {
            return $result;
        }

        // Worst case scenario, make a guess based on Laravel version
        return $this->fallbackToGuessFromLaravelVersionNumber($table, $column);
    }

    private function fallbackToGuessFromLaravelVersionNumber(string $table, string $column): IntegerField
    {
        // Trigger a notice to help folks finding the right path when shit hits the fan
        trigger_error(
            sprintf("Could not detect type for the `%s.%s` relation.\nSet the %s config entry to either `%s` or `%s`.",
                $table, $column, static::CONFIG_ROOT . ".$table.$column", 'int [unsigned]', 'bigint [unsigned]'
            ), E_USER_NOTICE
        );

        return (new FromLaravelVersionDetector())->run($table, $column);
    }
}
