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
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;
use LogicException;

class LaravelMigrationCompatibilityProvider extends ServiceProvider
{
    public function boot()
    {
        Blueprint::macro('intOrBigIntBasedOnRelated', function (string $column, Builder $builder, string $basedOn, bool $autoIncrement = false, bool $unsigned = false) {
            $related = explode('.', $basedOn);
            $type = ColumnTypeDetector::getType($builder->getConnection()->getPdo(), $related[0], $related[1]);

            if ($type->isInteger()) {
                return $this->integer($column, false, $type->isUnsigned());
            }

            if ($type->isBigint()) {
                return $this->bigInteger($column, false, $type->isUnsigned());
            }

            throw new LogicException("Could not determine field type for `$column`");
        });
    }
}
