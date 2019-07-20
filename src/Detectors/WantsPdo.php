<?php
/**
 * Contains the WantsPdo trait.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-20
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Detectors;

use PDO;

trait WantsPdo
{
    /** @var PDO */
    private $pdo;

    public function wantsPdo(): bool
    {
        return true;
    }

    public function setPdo(PDO $pdo): void
    {
        $this->pdo = $pdo;
    }
}
