<?php

namespace Konekt\LaravelMigrationCompatibility;

use PDO;

class ColumnTypeDetector
{
    /** @var PDO */
    private $pdo;

    public static function getType(PDO $pdo, string $table, string $column)
    {
        return (new self($pdo))->getColumnType($table, $column);
    }

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getColumnType(string $table, string $column)
    {
        $statement = $this->pdo->query(
            sprintf('SELECT * from %s LIMIT 1', $this->pdo->quote($table))
        );

        $columns = [];
        foreach (range(0, $statement->columnCount() - 1) as $index) {
            $meta = $statement->getColumnMeta($index);
            $columns[$meta['name']] = $meta;
        }

        return $columns[$column]['native_type'];
    }
}
