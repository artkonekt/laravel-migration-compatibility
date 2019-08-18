<?php
/**
 * Contains the MySqlDetector class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-20
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Detectors;

use Illuminate\Support\Str;
use Konekt\LaravelMigrationCompatibility\Contracts\FieldTypeDetector;
use Konekt\LaravelMigrationCompatibility\IntegerField;
use PDO;

class MySqlDetector implements FieldTypeDetector
{
    use WantsPdo;

    public function __construct(PDO $pdo)
    {
        $this->setPdo($pdo);
    }

    public function run(string $table, string $column): IntegerField
    {
        if (!$this->tableExists($table)) {
            return IntegerField::UNKNOWN();
        }

        $colDef = $this->getColumnMeta($table, $column);
        if (null === $colDef) {
            return IntegerField::UNKNOWN();
        }

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

    private function getColumnMeta(string $table, string $column): ?array
    {
        $statement = $this->pdo->query(sprintf('DESCRIBE `%s`', $table));
        $meta      = collect($statement->fetchAll(PDO::FETCH_ASSOC))->keyBy('Field');

        return $meta->get($column);
    }

    private function tableExists(string $table): bool
    {
        $tables = $this->pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_ASSOC);
        $tables = collect($tables)->mapWithKeys(function (array $item) {
            $value = array_values($item)[0];

            return [$value => true];
        });

        return $tables->has($table);
    }
}
