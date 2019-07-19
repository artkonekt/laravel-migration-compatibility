<?php

namespace Konekt\LaravelMigrationCompatibility;

use Illuminate\Support\Facades\App;
use PDO;
use PDOException;

class ColumnTypeDetector
{
    private const CONFIG_ROOT = 'migration.compatibility.map';

    /** @var PDO */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function getType(PDO $pdo, string $table, string $column)
    {
        return (new self($pdo))->getColumnType($table, $column);
    }

    public function getColumnType(string $table, string $column)
    {
        try {
            $statement = $this->pdo->query(
                sprintf('SELECT * from %s LIMIT 1', $this->pdo->quote($table))
            );

            $columns = [];
            foreach (range(0, $statement->columnCount() - 1) as $index) {
                $meta                   = $statement->getColumnMeta($index);
                $columns[$meta['name']] = $meta;
            }
        } catch (PDOException $exception) {
            return $this->detectFromConfiguration($table, $column);
        }

        if (array_key_exists($column, $columns)
            &&
            array_key_exists('native_type', $columns[$column])
        ) {
            return $columns[$column]['native_type'];
        }

        return $this->detectFromConfiguration($table, $column) ?: $this->fallbackToGuessFromLaravelVersionNumber($table, $column);
    }

    private function detectFromConfiguration(string $table, string $column): string
    {
        return config(static::CONFIG_ROOT . ".$table.$column");
    }

    private function fallbackToGuessFromLaravelVersionNumber(string $table, string $column): string
    {
        // Trigger a notice to help folks finding the right path when shit hits the fan
        trigger_error(
            sprintf("Could not detect type (int/bigInt) for the `%s.%s` relation.\nSet the %s config entry to either `%s` or `%s`.",
                $table, $column, static::CONFIG_ROOT . ".$table.$column", 'integer', 'bigInteger'
            ), E_USER_NOTICE
        );

        if (version_compare(App::version(), '5.8.0', '>=')) {
            return 'bigInteger';
        }

        return 'integer';
    }
}
