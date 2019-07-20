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
        return IntegerField::UNKNOWN();
    }
}
