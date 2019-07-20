<?php

namespace Konekt\LaravelMigrationCompatibility;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
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
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $method = 'get' . ucfirst(strtolower($driver)) . 'Type';
        $result = null;

        // Attempt to obtain from database
        if (method_exists($this, $method)) {
            $result = $this->{$method}($table, $column);
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

    private function getMysqlType(string $table, string $column): IntegerField
    {
        $tables = $this->pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_ASSOC);
        $tables = collect($tables)->mapWithKeys(function (array $item) {
            $value = array_values($item)[0];
            return [$value => true];
        });

        if (!$tables->has($table)) {
            return IntegerField::UNKNOWN();
        }

        $statement = $this->pdo->query(sprintf('DESCRIBE `%s`', $table));
        $meta = collect($statement->fetchAll(PDO::FETCH_ASSOC))->keyBy('Field');
        $colDef = $meta->get($column);
        $nativeType = strtolower($colDef['Type']);

        if (Str::startsWith($nativeType, 'bigint')) {
            $result = IntegerField::BIGINT();
        } elseif (Str::startsWith($nativeType, 'int')) {
            $result = IntegerField::INTEGER();
        } else {
            return IntegerField::UNKNOWN();
        }

        $result->unsigned(Str::contains($nativeType, 'unsigned'));

        return $result;
    }

    private function getPgsqlType(string $table, string $column): IntegerField
    {

    }

    private function getSqliteType(string $table, string $column): IntegerField
    {
        $statement = $this->pdo->query(
            sprintf('PRAGMA table_info(%s);', $this->pdo->quote($table))
        );

        $meta = collect($statement->fetchAll(PDO::FETCH_ASSOC))->keyBy('name');
        $colDef = $meta->get($column);

        return IntegerField::create($colDef['type'] ?? null);
    }
}
