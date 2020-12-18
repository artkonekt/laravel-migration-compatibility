<?php
/**
 * Contains the SqliteDetector class.
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

class SqliteDetector implements FieldTypeDetector
{
    use WantsPdo;

    public function __construct(PDO $pdo)
    {
        $this->setPdo($pdo);
    }

    public function run(string $table, string $column): IntegerField
    {
        $statement = $this->pdo->query(
            sprintf('PRAGMA table_info(%s);', $this->pdo->quote($table))
        );

        $meta   = collect($statement->fetchAll(PDO::FETCH_ASSOC))->keyBy('name');
        $colDef = $meta->get($column);
        $type   = isset($colDef['type']) ? mb_strtolower($colDef['type']) : null;

        return IntegerField::create($type);
    }
}
