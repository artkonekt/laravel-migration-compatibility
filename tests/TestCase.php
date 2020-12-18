<?php
/**
 * Contains the TestCase class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-17
 *
 */

namespace Konekt\LaravelMigrationCompatibility\Tests;

use Konekt\LaravelMigrationCompatibility\LaravelMigrationCompatibilityProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use PDO;

abstract class TestCase extends Orchestra
{
    /** @var PDO */
    protected $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = $this->app->make('db')->connection()->getPdo();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        $providers = [
            LaravelMigrationCompatibilityProvider::class
        ];

        return $providers;
    }

    /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $engine = env('TEST_DB_ENGINE', 'sqlite');

        $app['config']->set('database.default', $engine);
        $app['config']->set('database.connections.' . $engine, [
            'driver'   => $engine,
            'database' => 'sqlite' == $engine ? ':memory:' : 'migration_compat_test',
            'prefix'   => '',
            'host'     => env('TEST_DB_HOST', '127.0.0.1'),
            'username' => env('TEST_DB_USERNAME', 'pgsql' === $engine ? 'postgres' : 'root'),
            'password' => env('TEST_DB_PASSWORD', ''),
            'port' => env('TEST_DB_PORT'),
        ]);

        if ('pgsql' === $engine) {
            $app['config']->set("database.connections.{$engine}.charset", 'utf8');
        }
    }

    protected function tableExists(string $table): bool
    {
        try {
            $result = $this->pdo->query("SELECT 1 FROM $table LIMIT 1");
        } catch (Exception $e) {
            return false;
        }

        return false !== $result;
    }

    protected function isLaravel58OrHigher(): bool
    {
        return version_compare($this->app->version(), '5.8.0', '>=');
    }

    protected function getDefaultPlatformTypeConfig(): string
    {
        if ($this->isLaravel58OrHigher()) {
            return 'bigint unsigned';
        }

        return 'int unsigned';
    }
}
