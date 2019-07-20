<?php
/**
 * Contains the Detector interface.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-20
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Contracts;

use Konekt\LaravelMigrationCompatibility\IntegerField;
use PDO;

interface FieldTypeDetector
{
    public function run(string $table, string $column): IntegerField;

    public function wantsPdo(): bool;

    public function setPdo(PDO $pdo): void;
}
