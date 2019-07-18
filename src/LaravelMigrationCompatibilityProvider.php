<?php
/**
 * Contains the LaravelMigrationCompatibilityProvider class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-17
 *
 */

namespace Konekt\LaravelMigrationCompatibility;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class LaravelMigrationCompatibilityProvider extends ServiceProvider
{
    public function boot()
    {
        Blueprint::macro('intOrBigInt', function(...$args) {
            return $this->bigInteger(...$args);
        });
    }
}
