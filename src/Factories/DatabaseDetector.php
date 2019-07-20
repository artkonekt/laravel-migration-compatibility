<?php
/**
 * Contains the DatabaseDetector class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-20
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Factories;

use Konekt\LaravelMigrationCompatibility\Contracts\FieldTypeDetector;
use Konekt\LaravelMigrationCompatibility\Detectors\MySqlDetector;
use Konekt\LaravelMigrationCompatibility\Detectors\PostgresDetector;
use Konekt\LaravelMigrationCompatibility\Detectors\SqliteDetector;
use PDO;

class DatabaseDetector
{
    public static function fromPdoDriver(PDO $pdo): ?FieldTypeDetector
    {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

        switch ($driver) {
            case 'mysql':
                return new MySqlDetector($pdo);
                break;
            case 'pgsql':
                return new PostgresDetector($pdo);
                break;
            case 'sqlite':
                return new SqliteDetector($pdo);
                break;
        }

        return null;
    }
}
