# Laravel Migration Compatibility

[![Travis Build Status](https://img.shields.io/travis/com/artkonekt/laravel-migration-compatibility.svg?style=flat-square)](https://travis-ci.com/artkonekt/laravel-migration-compatibility)
[![Packagist Stable Version](https://img.shields.io/packagist/v/konekt/laravel-migration-compatibility.svg?style=flat-square&label=stable)](https://packagist.org/packages/konekt/laravel-migration-compatibility)
[![Packagist downloads](https://img.shields.io/packagist/dt/konekt/laravel-migration-compatibility.svg?style=flat-square)](https://packagist.org/packages/konekt/laravel-migration-compatibility)
[![StyleCI](https://styleci.io/repos/197359334/shield?branch=master)](https://styleci.io/repos/197359334)
[![MIT Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)

## Laravel 5.8 And The BigInt Problem

As of [Laravel 5.8](https://github.com/laravel/framework/pull/26472), migration stubs use the
`bigIncrements` method on ID columns by default. Previously, ID columns were created using the
increments method.

Foreign key columns must be of the same type. Therefore, a column created using the increments
method can not reference a column created using the bigIncrements method.

This small change is a big
[source of problems](https://laraveldaily.com/be-careful-laravel-5-8-added-bigincrements-as-defaults/)
for **packages** that define references to the **default Laravel user table**.

This package helps solving this problem by extending Laravel's `Blueprint` class by a method that
can detect the actual referenced field type:

```php
class CreateProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->increments('id');

            // Make `user_id` field the same type as the `id` field of the `user` table:
            $table->intOrBigIntBasedOnRelated('user_id', Schema::connection(null), 'users.id');

            //...

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }
//...
```

## Installation

```bash
composer require konekt/laravel-migration-compatibility
```

## Documentation

For detailed usage and examples go to the [Documentation](https://konekt.dev/laravel-migration-compatibility)
or refer to the markdown files in the `docs/` folder of this repo.

For the list of changes read the [Changelog](Changelog.md).
