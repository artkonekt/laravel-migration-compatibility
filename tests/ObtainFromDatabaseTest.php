<?php
/**
 * Contains the ObtainFromDatabaseTest class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-19
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Tests;

class ObtainFromDatabaseTest extends TestCase
{
    /** @test */
    public function obtains_field_type_from_db_when_table_was_already_present_before_migration_execution()
    {
        $this->artisan('migrate:reset');
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/examples');

        $this->assertTrue($this->tableExists('users'));
        $this->assertTrue($this->tableExists('profiles'));

        $this->pdo->query("insert into users (name, email, password) values ('asd', 'qwe', 'zxc')");
        $this->pdo->query("insert into profiles (user_id) values ({$this->pdo->lastInsertId()})");

        $select     = $this->pdo->query('SELECT * from users limit 1');
        $userIdMeta = $select->getColumnMeta(0);

        $select            = $this->pdo->query('SELECT * from profiles limit 1');
        $profileUserIdMeta = $select->getColumnMeta(1);

        $this->assertEquals('id', $userIdMeta['name']);
        $this->assertEquals('user_id', $profileUserIdMeta['name']);

        $this->assertEquals($userIdMeta['native_type'], $profileUserIdMeta['native_type']);
    }
}
