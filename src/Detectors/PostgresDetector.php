<?php
/**
 * Contains the PostgresDetector class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-20
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Detectors;

use Konekt\LaravelMigrationCompatibility\Contracts\FieldTypeDetector;
use Konekt\LaravelMigrationCompatibility\IntegerField;
use PDO;

class PostgresDetector implements FieldTypeDetector
{
    use WantsPdo;

    public function __construct(PDO $pdo)
    {
        $this->setPdo($pdo);
    }

    public function run(string $table, string $column): IntegerField
    {
        $colDef = $this->getColumnMeta($table, $column);

        if (!is_array($colDef)) {
            return IntegerField::UNKNOWN();
        }

        if ('bigint' === $colDef['data_type']) {
            return IntegerField::BIGINT();
        } elseif ('integer' === $colDef['data_type']) {
            return IntegerField::INTEGER();
        }

        return IntegerField::UNKNOWN();
    }

    private function getColumnMeta(string $table, string $column): ?array
    {
        $sql = 'SELECT * FROM information_schema.columns WHERE table_name = ' .
            "'$table' AND column_name = '$column'";
        $meta = collect($this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC))->keyBy('column_name');

        return $meta->get($column);
    }
}
