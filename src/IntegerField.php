<?php
/**
 * Contains the IntegerField class.
 *
 * @copyright   Copyright (c) 2019 Attila Fulop
 * @author      Attila Fulop
 * @license     MIT
 * @since       2019-07-19
 *
 */

namespace Konekt\LaravelMigrationCompatibility;

use Konekt\Enum\Enum;

/**
 * @method static IntegerField UNKNOWN()
 * @method static IntegerField BIGINT()
 * @method static IntegerField INTEGER()
 * @method bool isUnknown()
 * @method bool isInteger()
 * @method bool isBigint()
 */
class IntegerField extends Enum
{
    const UNKNOWN = null;
    const BIGINT  = 'bigint';
    const INTEGER = 'integer';

    protected $unsigned = false;

    public function isUnsigned(): bool
    {
        return $this->unsigned;
    }

    public function unsigned(bool $value = true): self
    {
        $this->unsigned = $value;

        return $this;
    }
}
