<?php
/**
 * Contains the FallbackTest class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-08-18
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Tests;

use PDO;

class FallbackTest extends TestCase
{
    /** @test */
    public function detects_the_default_type_from_laravel_version()
    {
        // Make sure Config is empty
        $this->app['config']->set("migration.compatibility.map", null);
        $this->artisan('migrate:reset');
        $this->loadMigrationsFrom(__DIR__ . '/chaos-example');

        $this->assertTrue($this->tableExists('chaos_profiles'));

        $this->pdo->query("insert into chaos_profiles (user_id) values ({$this->pdo->lastInsertId()})");

        $select            = $this->pdo->query('SELECT * from chaos_profiles limit 1');
        $profileUserIdMeta = $select->getColumnMeta(1);

        $this->assertEquals('user_id', $profileUserIdMeta['name']);

        $this->assertEquals($this->expectedType(), $profileUserIdMeta['native_type']);
    }

    public function expectedType(): string
    {
        switch ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) {
            case 'mysql':
                return $this->isLaravel58OrHigher() ? 'LONGLONG' : 'LONG';
                break;
            case 'pgsql':
                return $this->isLaravel58OrHigher() ? 'int8' : 'int4';
                break;
            case 'sqlite':
                return 'integer';
                break;
        }

        return '¯\_(ツ)_/¯';
    }
}
