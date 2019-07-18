<?php
/**
 * Contains the BlueprintMacroTest class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-18
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Konekt\LaravelMigrationCompatibility\Tests\Dummies\Profile;

class BlueprintMacroTest extends TestCase
{
    /** @test */
    public function can_obtain_the_field_type_from_a_foreign_table_field()
    {
        $this->loadMigrationsFrom(__DIR__ . '/examples');
        $this->artisan('migrate:reset');
        $this->loadLaravelMigrations();
        $this->artisan('migrate', ['--force' => true]);

        $this->assertTrue($this->tableExists('users'));
        $this->assertTrue($this->tableExists('profiles'));

        $this->pdo->query('insert into users (name, email, password) values ("asd", "qwe", "zxc")');
        $this->pdo->query("insert into profiles (user_id) values ({$this->pdo->lastInsertId()})");

        $select = $this->pdo->query('SELECT * from users limit 1');
        $userIdMeta = $select->getColumnMeta(0);

        $select = $this->pdo->query('SELECT * from profiles limit 1');
        $profileUserIdMeta = $select->getColumnMeta(1);

        $this->assertEquals('id', $userIdMeta['name']);
        $this->assertEquals('user_id', $profileUserIdMeta['name']);

        $this->assertEquals($userIdMeta['native_type'], $profileUserIdMeta['native_type']);
    }
}
