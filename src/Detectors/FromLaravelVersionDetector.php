<?php
/**
 * Contains the FromLaravelVersionDetector class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-08-18
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Detectors;

use Illuminate\Support\Facades\App;
use Konekt\LaravelMigrationCompatibility\Contracts\FieldTypeDetector;
use Konekt\LaravelMigrationCompatibility\IntegerField;
use PDO;

class FromLaravelVersionDetector implements FieldTypeDetector
{
    public function run(string $table, string $column): IntegerField
    {
        if (version_compare(App::version(), '5.8.0', '>=')) {
            return IntegerField::BIGINT()->unsigned();
        }

        return IntegerField::INTEGER()->unsigned();
    }

    public function wantsPdo(): bool
    {
        return false;
    }

    public function setPdo(PDO $pdo): void
    {
    }
}
